<?php

declare(strict_types=1);

namespace app\models\forms\admin;

use Da\User\Form\LoginForm as BaseForm;
use Yii;

class LoginAdminForm extends BaseForm
{
    /**
     * {@inheritdoc}
     *
     * @throws InvalidSecretKeyException
     */
    public function rules()
    {
        return [
            'requiredFields' => [['login', 'password'], 'required'],
            'loginTrim' => ['login', 'trim'],
            'twoFactorAuthenticationCodeTrim' => ['twoFactorAuthenticationCode', 'trim'],
            'passwordValidate' => [
                'password',
                function ($attribute) {
                    if ($this->user === null ||
                        ! $this->securityHelper->validatePassword($this->password, $this->user->password_hash) ||
                        ! ($this->login === $this->user->username)) {
                        $this->addError($attribute, Yii::t('usuario', 'Invalid login or password'));
                    }
                },
            ],
        ];
    }

    /**
     * Validates form and logs the user in.
     * @return bool whether the user is logged in successfully
     */
    public function login()
    {
        if ($this->validate()) {
            $duration = $this->rememberMe ? $this->module->rememberLoginLifespan : 0;

            return Yii::$app->getUser()->login($this->user, $duration);
        }

        return false;
    }
}
