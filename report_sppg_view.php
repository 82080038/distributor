<?php
require_once __DIR__ . DIRECTORY_SEPARATOR . 'config.php';

$defaultA = 'SPPG_JAN2026';
$defaultB = 'SPPG_JAN2026_MINGGU1';

$sppgA = isset($_GET['sppg_a']) && $_GET['sppg_a'] !== '' ? $_GET['sppg_a'] : $defaultA;
$sppgB = isset($_GET['sppg_b']) && $_GET['sppg_b'] !== '' ? $_GET['sppg_b'] : $defaultB;
$startDate = isset($_GET['start_date']) && $_GET['start_date'] !== '' ? parse_date_id_to_db($_GET['start_date']) : '';
$endDate = isset($_GET['end_date']) && $_GET['end_date'] !== '' ? parse_date_id_to_db($_GET['end_date']) : '';

$sppgIds = [];
$resIds = $conn->query("SELECT DISTINCT sppg_id FROM sppg_daily_material_demand ORDER BY sppg_id");
if ($resIds) {
    while ($row = $resIds->fetch_assoc()) {
        $sppgIds[] = $row['sppg_id'];
    }
    $resIds->close();
}

$rows = [];
if ($sppgA !== '' && $sppgB !== '' && $sppgA !== $sppgB) {
    if ($startDate !== '' && $endDate !== '') {
        $sql = "SELECT
                    d.demand_date,
                    d.material_code,
                    sm.material_name,
                    SUM(CASE WHEN d.sppg_id = ? THEN d.total_quantity_grams END) / 1000 AS qty_a,
                    SUM(CASE WHEN d.sppg_id = ? THEN d.total_quantity_grams END) / 1000 AS qty_b
                FROM sppg_daily_material_demand d
                LEFT JOIN sppg_materials sm
                    ON sm.material_code = d.material_code
                WHERE d.sppg_id IN (?, ?)
                AND d.demand_date BETWEEN ? AND ?
                GROUP BY d.demand_date, d.material_code, sm.material_name
                ORDER BY d.demand_date, sm.material_name, d.material_code";
        $stmt = $conn->prepare($sql);
        if ($stmt) {
            $stmt->bind_param('ssssss', $sppgA, $sppgB, $sppgA, $sppgB, $startDate, $endDate);
            $stmt->execute();
            $res = $stmt->get_result();
            if ($res) {
                while ($r = $res->fetch_assoc()) {
                    $rows[] = $r;
                }
                $res->close();
            }
            $stmt->close();
        }
    } elseif ($startDate !== '') {
        $sql = "SELECT
                    d.demand_date,
                    d.material_code,
                    sm.material_name,
                    SUM(CASE WHEN d.sppg_id = ? THEN d.total_quantity_grams END) / 1000 AS qty_a,
                    SUM(CASE WHEN d.sppg_id = ? THEN d.total_quantity_grams END) / 1000 AS qty_b
                FROM sppg_daily_material_demand d
                LEFT JOIN sppg_materials sm
                    ON sm.material_code = d.material_code
                WHERE d.sppg_id IN (?, ?)
                AND d.demand_date >= ?
                GROUP BY d.demand_date, d.material_code, sm.material_name
                ORDER BY d.demand_date, sm.material_name, d.material_code";
        $stmt = $conn->prepare($sql);
        if ($stmt) {
            $stmt->bind_param('sssss', $sppgA, $sppgB, $sppgA, $sppgB, $startDate);
            $stmt->execute();
            $res = $stmt->get_result();
            if ($res) {
                while ($r = $res->fetch_assoc()) {
                    $rows[] = $r;
                }
                $res->close();
            }
            $stmt->close();
        }
    } elseif ($endDate !== '') {
        $sql = "SELECT
                    d.demand_date,
                    d.material_code,
                    sm.material_name,
                    SUM(CASE WHEN d.sppg_id = ? THEN d.total_quantity_grams END) / 1000 AS qty_a,
                    SUM(CASE WHEN d.sppg_id = ? THEN d.total_quantity_grams END) / 1000 AS qty_b
                FROM sppg_daily_material_demand d
                LEFT JOIN sppg_materials sm
                    ON sm.material_code = d.material_code
                WHERE d.sppg_id IN (?, ?)
                AND d.demand_date <= ?
                GROUP BY d.demand_date, d.material_code, sm.material_name
                ORDER BY d.demand_date, sm.material_name, d.material_code";
        $stmt = $conn->prepare($sql);
        if ($stmt) {
            $stmt->bind_param('sssss', $sppgA, $sppgB, $sppgA, $sppgB, $endDate);
            $stmt->execute();
            $res = $stmt->get_result();
            if ($res) {
                while ($r = $res->fetch_assoc()) {
                    $rows[] = $r;
                }
                $res->close();
            }
            $stmt->close();
        }
    } else {
        $sql = "SELECT
                    d.demand_date,
                    d.material_code,
                    sm.material_name,
                    SUM(CASE WHEN d.sppg_id = ? THEN d.total_quantity_grams END) / 1000 AS qty_a,
                    SUM(CASE WHEN d.sppg_id = ? THEN d.total_quantity_grams END) / 1000 AS qty_b
                FROM sppg_daily_material_demand d
                LEFT JOIN sppg_materials sm
                    ON sm.material_code = d.material_code
                WHERE d.sppg_id IN (?, ?)
                GROUP BY d.demand_date, d.material_code, sm.material_name
                ORDER BY d.demand_date, sm.material_name, d.material_code";
        $stmt = $conn->prepare($sql);
        if ($stmt) {
            $stmt->bind_param('ssss', $sppgA, $sppgB, $sppgA, $sppgB);
            $stmt->execute();
            $res = $stmt->get_result();
            if ($res) {
                while ($r = $res->fetch_assoc()) {
                    $rows[] = $r;
                }
                $res->close();
            }
            $stmt->close();
        }
    }
} elseif ($sppgA !== '') {
    if ($startDate !== '' && $endDate !== '') {
        $sql = "SELECT
                    d.demand_date,
                    d.material_code,
                    sm.material_name,
                    SUM(d.total_quantity_grams) / 1000 AS qty_a
                FROM sppg_daily_material_demand d
                LEFT JOIN sppg_materials sm
                    ON sm.material_code = d.material_code
                WHERE d.sppg_id = ?
                AND d.demand_date BETWEEN ? AND ?
                GROUP BY d.demand_date, d.material_code, sm.material_name
                ORDER BY d.demand_date, sm.material_name, d.material_code";
        $stmt = $conn->prepare($sql);
        if ($stmt) {
            $stmt->bind_param('sss', $sppgA, $startDate, $endDate);
            $stmt->execute();
            $res = $stmt->get_result();
            if ($res) {
                while ($r = $res->fetch_assoc()) {
                    $r['qty_b'] = null;
                    $rows[] = $r;
                }
                $res->close();
            }
            $stmt->close();
        }
    } elseif ($startDate !== '') {
        $sql = "SELECT
                    d.demand_date,
                    d.material_code,
                    sm.material_name,
                    SUM(d.total_quantity_grams) / 1000 AS qty_a
                FROM sppg_daily_material_demand d
                LEFT JOIN sppg_materials sm
                    ON sm.material_code = d.material_code
                WHERE d.sppg_id = ?
                AND d.demand_date >= ?
                GROUP BY d.demand_date, d.material_code, sm.material_name
                ORDER BY d.demand_date, sm.material_name, d.material_code";
        $stmt = $conn->prepare($sql);
        if ($stmt) {
            $stmt->bind_param('ss', $sppgA, $startDate);
            $stmt->execute();
            $res = $stmt->get_result();
            if ($res) {
                while ($r = $res->fetch_assoc()) {
                    $r['qty_b'] = null;
                    $rows[] = $r;
                }
                $res->close();
            }
            $stmt->close();
        }
    } elseif ($endDate !== '') {
        $sql = "SELECT
                    d.demand_date,
                    d.material_code,
                    sm.material_name,
                    SUM(d.total_quantity_grams) / 1000 AS qty_a
                FROM sppg_daily_material_demand d
                LEFT JOIN sppg_materials sm
                    ON sm.material_code = d.material_code
                WHERE d.sppg_id = ?
                AND d.demand_date <= ?
                GROUP BY d.demand_date, d.material_code, sm.material_name
                ORDER BY d.demand_date, sm.material_name, d.material_code";
        $stmt = $conn->prepare($sql);
        if ($stmt) {
            $stmt->bind_param('ss', $sppgA, $endDate);
            $stmt->execute();
            $res = $stmt->get_result();
            if ($res) {
                while ($r = $res->fetch_assoc()) {
                    $r['qty_b'] = null;
                    $rows[] = $r;
                }
                $res->close();
            }
            $stmt->close();
        }
    } else {
        $sql = "SELECT
                    d.demand_date,
                    d.material_code,
                    sm.material_name,
                    SUM(d.total_quantity_grams) / 1000 AS qty_a
                FROM sppg_daily_material_demand d
                LEFT JOIN sppg_materials sm
                    ON sm.material_code = d.material_code
                WHERE d.sppg_id = ?
                GROUP BY d.demand_date, d.material_code, sm.material_name
                ORDER BY d.demand_date, sm.material_name, d.material_code";
        $stmt = $conn->prepare($sql);
        if ($stmt) {
            $stmt->bind_param('s', $sppgA);
            $stmt->execute();
            $res = $stmt->get_result();
            if ($res) {
                while ($r = $res->fetch_assoc()) {
                    $r['qty_b'] = null;
                    $rows[] = $r;
                }
                $res->close();
            }
            $stmt->close();
        }
    }
}

?>
<div class="row">
    <div class="col-md-12">
        <h1 class="h3 mb-3">Laporan SPPG per Bahan per Hari</h1>
        <div class="card mb-3">
            <div class="card-body">
                <form class="row g-2" method="get" action="report_sppg.php">
                    <div class="col-md-3">
                        <label class="form-label">SPPG A (rencana)</label>
                        <select name="sppg_a" class="form-select">
                            <option value="">Pilih SPPG</option>
                            <?php foreach ($sppgIds as $id): ?>
                            <option value="<?php echo htmlspecialchars($id, ENT_QUOTES, 'UTF-8'); ?>" <?php echo $id === $sppgA ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($id, ENT_QUOTES, 'UTF-8'); ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">SPPG B (aktual)</label>
                        <select name="sppg_b" class="form-select">
                            <option value="">Pilih SPPG</option>
                            <?php foreach ($sppgIds as $id): ?>
                            <option value="<?php echo htmlspecialchars($id, ENT_QUOTES, 'UTF-8'); ?>" <?php echo $id === $sppgB ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($id, ENT_QUOTES, 'UTF-8'); ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Mulai Tanggal</label>
                        <div class="input-group input-group-sm">
                            <input
                                type="text"
                                name="start_date"
                                id="sppg_start_date"
                                class="form-control date-input"
                                inputmode="numeric"
                                pattern="\d{2}-\d{2}-\d{4}"
                                placeholder="dd-mm-yyyy"
                                value="<?php echo htmlspecialchars(format_date_id($startDate), ENT_QUOTES, 'UTF-8'); ?>"
                                aria-label="Mulai tanggal laporan SPPG"
                                data-calendar-button="#btn_sppg_start_date"
                            >
                            <button
                                class="btn btn-outline-secondary"
                                type="button"
                                id="btn_sppg_start_date"
                                aria-label="Pilih mulai tanggal laporan SPPG"
                            >
                                📅
                            </button>
                        </div>
                        <div class="invalid-feedback"></div>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Sampai Tanggal</label>
                        <div class="input-group input-group-sm">
                            <input
                                type="text"
                                name="end_date"
                                id="sppg_end_date"
                                class="form-control date-input"
                                inputmode="numeric"
                                pattern="\d{2}-\d{2}-\d{4}"
                                placeholder="dd-mm-yyyy"
                                value="<?php echo htmlspecialchars(format_date_id($endDate), ENT_QUOTES, 'UTF-8'); ?>"
                                aria-label="Sampai tanggal laporan SPPG"
                                data-calendar-button="#btn_sppg_end_date"
                            >
                            <button
                                class="btn btn-outline-secondary"
                                type="button"
                                id="btn_sppg_end_date"
                                aria-label="Pilih sampai tanggal laporan SPPG"
                            >
                                📅
                            </button>
                        </div>
                        <div class="invalid-feedback"></div>
                    </div>
                    <div class="col-md-2 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary w-100">Terapkan</button>
                    </div>
                </form>
            </div>
        </div>
        <div class="card">
            <div class="card-body">
                <?php if (empty($rows)): ?>
                    <p class="mb-0">Belum ada data untuk kombinasi SPPG yang dipilih.</p>
                <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-sm table-striped align-middle">
                        <thead>
                            <tr>
                                <th>Tanggal</th>
                                <th>Kode Bahan</th>
                                <th>Nama Bahan</th>
                                <th class="text-end">Qty SPPG A (kg)</th>
                                <th class="text-end">Qty SPPG B (kg)</th>
                                <th class="text-end">Selisih B - A (kg)</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($rows as $r): ?>
                            <?php
                                $qtyA = isset($r['qty_a']) ? (float)$r['qty_a'] : 0.0;
                                $qtyB = isset($r['qty_b']) ? (float)$r['qty_b'] : null;
                                $diff = $qtyB !== null ? $qtyB - $qtyA : null;
                            ?>
                            <tr>
                                <td><?php echo htmlspecialchars(format_date_id($r['demand_date']), ENT_QUOTES, 'UTF-8'); ?></td>
                                <td><?php echo htmlspecialchars($r['material_code'], ENT_QUOTES, 'UTF-8'); ?></td>
                                <td><?php echo htmlspecialchars($r['material_name'] ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
                                <td class="text-end"><?php echo number_format($qtyA, 3, ',', '.'); ?></td>
                                <td class="text-end">
                                    <?php echo $qtyB !== null ? number_format($qtyB, 3, ',', '.') : '-'; ?>
                                </td>
                                <td class="text-end">
                                    <?php echo $diff !== null ? number_format($diff, 3, ',', '.') : '-'; ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
