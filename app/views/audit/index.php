<!-- Content Header (Page header) -->
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Auditoría - Registros</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="<?php echo BASE_URL; ?>/dashboard">Inicio</a></li>
                    <li class="breadcrumb-item active">Auditoría</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<!-- Main content -->
<section class="content">
    <div class="container-fluid">

        <?php if(isset($_SESSION['success'])): ?>
            <div class="alert alert-success alert-dismissible">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                <i class="icon fas fa-check"></i>
                <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
            </div>
        <?php endif; ?>

        <?php if(isset($_SESSION['error'])): ?>
            <div class="alert alert-danger alert-dismissible">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                <i class="icon fas fa-ban"></i>
                <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
            </div>
        <?php endif; ?>

        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Registros de auditoría recientes</h3>
                <div class="card-tools">
                    <a href="<?php echo BASE_URL; ?>/audit/triggers" class="btn btn-secondary btn-sm">
                        <i class="fas fa-bell mr-1"></i> Historial de Triggers
                    </a>
                </div>
            </div>
            <div class="card-body">
                <?php if($logs && count($logs) > 0): ?>
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Tabla</th>
                                    <th>Operación</th>
                                    <th>Fecha</th>
                                    <th>Usuario</th>
                                    <th>Antes</th>
                                    <th>Después</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($logs as $log): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($log['pk_value']); ?></td>
                                    <td><?php echo htmlspecialchars($log['table_name']); ?></td>
                                    <td><?php echo htmlspecialchars(ucfirst(strtolower($log['operation']))); ?></td>
                                    <td><?php echo $log['changed_at']; ?></td>
                                    <td><?php echo htmlspecialchars($log['changed_by']); ?></td>
                                    <td style="max-width:300px;">
                                        <?php
                                            $old = $log['old_values'];
                                            $old_dec = @json_decode($old, true);
                                            if (is_array($old_dec)) {
                                                echo '<div style="max-height:150px; overflow:auto; background:#f8f9fa; padding:6px;">';
                                                foreach ($old_dec as $k => $v) {
                                                    echo '<strong>' . htmlspecialchars($k) . ':</strong> ' . htmlspecialchars((string)$v) . '<br/>';
                                                }
                                                echo '</div>';
                                            } else {
                                                echo '<small>' . htmlspecialchars($old) . '</small>';
                                            }
                                        ?>
                                    </td>
                                    <td style="max-width:300px;">
                                        <?php
                                            $new = $log['new_values'];
                                            $new_dec = @json_decode($new, true);
                                            if (is_array($new_dec)) {
                                                echo '<div style="max-height:150px; overflow:auto; background:#f8f9fa; padding:6px;">';
                                                foreach ($new_dec as $k => $v) {
                                                    echo '<strong>' . htmlspecialchars($k) . ':</strong> ' . htmlspecialchars((string)$v) . '<br/>';
                                                }
                                                echo '</div>';
                                            } else {
                                                echo '<small>' . htmlspecialchars($new) . '</small>';
                                            }
                                        ?>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="alert alert-info text-center">
                        <i class="fas fa-info-circle mr-2"></i>
                        No hay registros de auditoría.
                    </div>
                <?php endif; ?>
            </div>
        </div>

    </div>
</section>
