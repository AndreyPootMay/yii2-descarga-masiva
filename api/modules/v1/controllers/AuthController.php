<?php

namespace api\modules\v1\controllers;

use api\modules\v1\models\LoginForm;
use app\models\UserAdmin;
use Yii;
use yii\rest\Controller;
use app\models\UserRefreshTokens;
use Da\User\Helper\SecurityHelper;
use Da\User\Query\UserQuery;

class AuthController extends Controller
{
    public function behaviors()
    {
        $behaviors = parent::behaviors();

        $behaviors['authenticator'] = [
            'class' => \sizeg\jwt\JwtHttpBearerAuth::class,
            'except' => [
                'login',
                'refresh-token',
                'options',
            ],
        ];

        return $behaviors;
    }

    private function generateJwt(UserAdmin $user)
    {
        $jwt = Yii::$app->jwt;
        $signer = $jwt->getSigner('HS256');
        $key = $jwt->getKey();
        $time = time();

        $jwtParams = Yii::$app->params['jwt'];

        return $jwt->getBuilder()
            ->issuedBy($jwtParams['issuer'])
            ->permittedFor($jwtParams['audience'])
            ->identifiedBy($jwtParams['id'], true)
            ->issuedAt($time)
            ->expiresAt($time + $jwtParams['expire'])
            ->withClaim('uid', $user->id)
            ->getToken($signer, $key);
    }

    /**
     * @throws yii\base\Exception
     */
    private function generateRefreshToken(
        UserAdmin $user,
        UserAdmin $impersonator = null
    ): UserRefreshTokens {
        $refreshToken = Yii::$app->security->generateRandomString(200);

        $userRefreshToken = new UserRefreshTokens([
            'urf_userID' => $user->id,
            'urf_token' => $refreshToken,
            'urf_ip' => Yii::$app->request->userIP,
            'urf_user_agent' => Yii::$app->request->userAgent,
            'urf_created' => gmdate('Y-m-d H:i:s'),
        ]);
        if (!$userRefreshToken->save(false)) {
            throw new \yii\web\ServerErrorHttpException('Failed to save the refresh token: ' . $userRefreshToken->getErrorSummary(true));
        }

        Yii::$app->response->cookies->add(
            new \yii\web\Cookie([
                'name' => 'refresh-token',
                'value' => $refreshToken,
                'httpOnly' => true,
                'sameSite' => 'none',
                'secure' => true,
                'path' => '/v1/auth/refresh-token',
            ])
        );

        return $userRefreshToken;
    }

    public function actionLogin()
    {
        $userQuery = new UserQuery(UserAdmin::class);
        $securityHelper = new SecurityHelper(new \yii\base\Security());

        $model = new LoginForm($userQuery, $securityHelper);
        if ($model->load(Yii::$app->request->getBodyParams(), '') && $model->login()) {
            $user = Yii::$app->user->identity;

            $token = $this->generateJwt($user);

            $this->generateRefreshToken($user);

            return [
                'user' => $user,
                'token' => (string) $token,
            ];
        } else {
            return $model->getFirstErrors();
        }
    }

    public function actionRefreshToken()
    {
        $refreshToken = Yii::$app->request->cookies->getValue('refresh-token', false);
        if (!$refreshToken) {
            return new \yii\web\UnauthorizedHttpException('No refresh token found.');
        }

        $userRefreshToken = UserRefreshTokens::findOne(['urf_token' => $refreshToken]);

        if (Yii::$app->request->getMethod() == 'POST') {
            // Getting new JWT after it has expired
            if (!$userRefreshToken) {
                return new \yii\web\UnauthorizedHttpException('The refresh token no longer exists.');
            }

            $user = UserAdmin::find()  //adapt this to your needs
                ->where(['userID' => $userRefreshToken->urf_userID])
                ->andWhere(['not', ['usr_status' => 'inactive']])
                ->one();
            if (!$user) {
                $userRefreshToken->delete();
                return new \yii\web\UnauthorizedHttpException('The user is inactive.');
            }

            $token = $this->generateJwt($user);

            return [
                'status' => 'ok',
                'token' => (string) $token,
            ];
        } elseif (Yii::$app->request->getMethod() == 'DELETE') {
            // Logging out
            if ($userRefreshToken && !$userRefreshToken->delete()) {
                return new \yii\web\ServerErrorHttpException('Failed to delete the refresh token.');
            }

            return ['status' => 'ok'];
        } else {
            return new \yii\web\UnauthorizedHttpException('The user is inactive.');
        }
    }
}
