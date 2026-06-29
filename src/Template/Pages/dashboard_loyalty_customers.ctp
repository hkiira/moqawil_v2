<div class="modal-header">
    <h5 class="modal-title mt-0" id="myModalLabel">
        <i class="fas fa-coins text-info mr-2"></i>Détails des Points de Fidélité (Calculés)
    </h5>
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
</div>
<div class="modal-body" style="max-height: 450px; overflow-y: auto;">
    <p class="text-muted text-small mb-3">
        Liste des clients ayant accumulé des points non réclamés pour la période du 
        <strong><?= h(date('d/m/Y', strtotime($startDate))) ?></strong> au <strong><?= h(date('d/m/Y', strtotime($endDate))) ?></strong>.
    </p>
    
    <?php if (!empty($customersPoints)): ?>
        <div class="table-responsive">
            <table class="table table-striped table-bordered table-hover mb-0">
                <thead class="thead-light">
                    <tr>
                        <th style="width: 15%;">ID Client</th>
                        <th>Nom du Client</th>
                        <th style="width: 25%; text-align: right;">Points de Fidélité</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($customersPoints as $customer): ?>
                        <tr>
                            <td><code><?= h($customer->customer_id) ?></code></td>
                            <td><strong><?= h($customer->customer_name ?: 'Client #' . $customer->customer_id) ?></strong></td>
                            <td style="text-align: right; font-weight: bold; font-size: 14px;" class="text-info">
                                <?= number_format($customer->total_points) ?> pts
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <div class="text-center p-5">
            <i class="far fa-frown fa-3x text-muted mb-3"></i>
            <p class="text-muted">Aucun client n'a de points calculés non réclamés pour cette période.</p>
        </div>
    <?php endif; ?>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-secondary waves-effect" data-dismiss="modal">Fermer</button>
</div>
