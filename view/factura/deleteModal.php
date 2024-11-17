<?php
if (!isset($row)) {
    $row = ["numero" => 0];
} ?>
<div class="modal fade" id="iddel<?= $row["numero"] ?>" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">¿Desea anular factura?</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Una vez anulado no se incluye en los resumenes.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                <a href="delete.php?numero=<?= $row[
                    "numero"
                ] ?>" type="button" class="btn btn-danger">Anular</a>
            </div>
        </div>
    </div>
</div>
