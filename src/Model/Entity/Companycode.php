<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Companycode Entity
 *
 * @property int $id
 * @property string|null $name
 * @property string $controleur
 * @property string $prefixe
 * @property int|null $compteur
 * @property int|null $statut
 * @property int $company_id
 *
 * @property \App\Model\Entity\Company $company
 */
class Companycode extends Entity
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
    protected $_accessible = [
        'name' => true,
        'controleur' => true,
        'prefixe' => true,
        'compteur' => true,
        'statut' => true,
        'company_id' => true,
        'company' => true,
    ];
}
