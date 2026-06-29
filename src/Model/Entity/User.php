<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;
use Cake\Auth\DefaultPasswordHasher;

/**
 * User Entity
 *
 * @property int $id
 * @property string $firstname
 * @property string $lastname
 * @property string|null $cin
 * @property \Cake\I18n\FrozenDate|null $birthday
 * @property string $username
 * @property string $email
 * @property string $password
 * @property int|null $role_id
 * @property int $company_id
 * @property int|null $statut
 * @property \Cake\I18n\FrozenTime|null $created
 * @property \Cake\I18n\FrozenTime|null $modified
 *
 * @property \App\Model\Entity\Role $role
 * @property \App\Model\Entity\Company $company
 * @property \App\Model\Entity\Accesuser[] $accesusers
 */
class User extends Entity
{
    /**
     * Fields that can be mass assigned using newEntity() or patchEntity().
     *
     * Note that when '*' is set to true, this allows all unspecified fields to
     * be mass assigned. For security purposes, it is advised to set '*' to false
     * (or remove it), and explicitly make individual fields accessible as needed.
     *
     * @var array
     */
    protected function _setPassword($password)
    {
        if (strlen($password) > 0) {
          return (new DefaultPasswordHasher)->hash($password);
        }
    }
    protected $_accessible = [
        'code' => true,
        'firstname' => true,
        'lastname' => true,
        'cin' => true,
        'birthday' => true,
        'username' => true,
        'email' => true,
        'password' => true,
        'grpassword' => true,
        'role_id' => true,
        'company_id' => true,
        'statut' => true,
        'created' => true,
        'categoryuser_id' => true,
        'categoryuser' => true,
        'modified' => true,
        'referral' => true,
        'role' => true,
        'app' => true,
        'company' => true,
        'accesusers' => true,
        'zoneusers' => true,
        'pofsusers' => true,
        'whusers' => true,
        'inventories' => true,
        'reports' => true,
        'slips' => true,
    ];

    /**
     * Fields that are excluded from JSON versions of the entity.
     *
     * @var array
     */
    protected $_hidden = [
        'password',
    ];
}
