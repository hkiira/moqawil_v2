<?php
namespace App\Model\Behavior;

use Cake\ORM\Behavior;
use Cake\Event\Event;
use Cake\ORM\Query;
use App\Utility\TenantManager;

class MultitenantBehavior extends Behavior
{
    public function beforeFind(Event $event, Query $query, \ArrayObject $options, $primary)
    {
        $tenantId = TenantManager::getCurrentTenantId();
        if ($tenantId !== null && $this->_table->hasField('company_id')) {
            $query->where([
                $this->_table->aliasField('company_id') => $tenantId
            ]);
        }
    }

    public function beforeSave(Event $event, $entity, \ArrayObject $options)
    {
        $tenantId = TenantManager::getCurrentTenantId();
        if ($tenantId !== null && $this->_table->hasField('company_id')) {
            if ($entity->isNew()) {
                $entity->set('company_id', $tenantId);
            }
        }
    }
}
