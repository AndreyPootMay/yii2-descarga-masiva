<?php

namespace app\models;

use Yii;
use Da\User\Model\User as BaseUser;
use yii\db\Query;
use yii\web\IdentityInterface;

class UserAdmin extends BaseUser implements IdentityInterface
{
    public $password_confirm;

    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'timestamp' => [
                'class' => 'yii\behaviors\TimestampBehavior',
            ],
        ];
    }

    /**
     * Getting the list of roles.
     * @return  string
     */
    public function getRoles()
    {
        $rows = new Query();

        $roles = $rows->select(['item_name'])
            ->from('auth_assignment')
            ->where(['user_id' => $this->id])
            ->all();

        if (empty($roles)) {
            return Yii::t('app', 'Sin definir');
        }

        $allRoles = '';

        foreach ($roles as $rol) {
            $allRoles .= $rol['item_name'] . ', ';
        }

        return trim($allRoles, ', ');
    }

    public static function findByUsername($username)
    {
        return static::findOne(['username' => $username]);
    }

    public static function findIdentityByAccessToken($token, $type = null)
    {
        return static::find()
        ->where(['id' => (string) $token->getClaim('uid')])
        ->andWhere('blocked_at IS NULL')
        ->one();
    }
}
