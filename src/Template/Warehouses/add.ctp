<?php if ($entrepot): ?>
    <?= $this->element('entrepot/adddepot')  ?>
<?php else: ?>
    <?= $this->element('entrepot/addentrepot')  ?>
<?php endif ?>