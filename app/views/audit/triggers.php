<!-- Content Header (Page header) -->
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Auditoría - Historial de Triggers</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="<?php echo BASE_URL; ?>/dashboard">Inicio</a></li>
                    <li class="breadcrumb-item active">Triggers</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">

        <?php if(isset($_SESSION['success'])): ?>
            <div class="alert alert-success alert-dismissible">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                <i class="icon fas fa-check"></i>
                <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
            </div>
        <?php endif; ?>

        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Historial de cambios de triggers</h3>
                <div class="card-tools">
                    <a href="<?php echo BASE_URL; ?>/audit/sync" class="btn btn-success btn-sm" onclick="return confirm('Ejecutar sincronización de triggers ahora?');">
                        <i class="fas fa-sync-alt mr-1"></i> Sincronizar ahora
                    </a>
                </div>
            </div>
            <div class="card-body">
                <?php if($items && count($items) > 0): ?>
                    <?php foreach($items as $it): ?>
                        <div class="mb-3">
                            <h5><?php echo htmlspecialchars($it['trigger_name']); ?> <small class="text-muted">(<?php echo htmlspecialchars($it['table_name']); ?>) - <?php echo $it['applied_at']; ?></small></h5>
                            <pre style="max-height:200px; overflow:auto; background:#f8f9fa; padding:10px; border:1px solid #e1e1e1;"><?php echo htmlspecialchars($it['ddl']); ?></pre>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="alert alert-info text-center">
                        <i class="fas fa-info-circle mr-2"></i>
                        No hay historial de triggers.
                    </div>
                <?php endif; ?>
            </div>
        </div>

    </div>
</section>
