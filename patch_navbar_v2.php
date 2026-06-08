<?php
$files = glob("*.php");

foreach($files as $file) {
    if ($file == "login.php" || $file == "register.php" || $file == "koneksi.php" || strpos($file, 'admin-') !== false) {
        continue;
    }

    $content = file_get_contents($file);

    // If it has already been processed with the if/else block, skip
    if (strpos($content, '<?php if ($is_logged_in): ?>') !== false) {
        continue;
    }

    // Pattern 1: The dropdown in subpages (kesehatan.php, dll)
    // <div class="dropdown mt-2 mt-lg-0">...</div>
    $pattern1 = '/<div class="dropdown mt-2 mt-lg-0">.*?<span class="fw-bold text-dark"[^>]*>Tata Difa<\/span>.*?<\/ul>\s*<\/div>/is';
    
    // Pattern 2: The dropdown in index.php
    $pattern2 = '/<div class="dropdown">\s*<div class="d-flex align-items-center gap-2" data-bs-toggle="dropdown".*?Tata Difa Ananda.*?<\/ul>\s*<\/div>/is';

    // Replacement 1 (Subpages)
    $rep1 = '<?php if ($is_logged_in): ?>
        <div class="dropdown mt-2 mt-lg-0">
          <div class="d-flex align-items-center bg-white border rounded-pill px-3 py-1 shadow-sm" data-bs-toggle="dropdown" style="cursor: pointer;">
            <div class="rounded-circle d-flex align-items-center justify-content-center fw-bold me-2" style="width: 25px; height: 25px; background-color: #FFE6F0; color: #E91E63; font-size: 11px;"><?= $initial ?></div>
            <span class="fw-bold text-dark" style="font-size: 13px;"><?= htmlspecialchars($user_nama) ?></span>
          </div>
          <ul class="dropdown-menu dropdown-menu-end shadow border-0 mt-2" style="border-radius: 12px; font-size: 14px;">
            <li><a class="dropdown-item py-2" href="#"><i class="fa-solid fa-user me-2 text-muted"></i> Profile</a></li>
            <li><a class="dropdown-item py-2" href="#"><i class="fa-solid fa-gear me-2 text-muted"></i> Setting</a></li>
            <li><a class="dropdown-item py-2" href="<?= $dashboard_url ?>"><i class="fa-solid fa-chart-line me-2 text-muted"></i> Dashboard</a></li>
            <li><hr class="dropdown-divider"></li>
            <li><a class="dropdown-item py-2 text-danger fw-bold" href="config/function_auth.php?action=logout"><i class="fa-solid fa-right-from-bracket me-2"></i> Logout</a></li>
          </ul>
        </div>
<?php else: ?>
        <a href="login.php" class="text-decoration-none d-flex align-items-center bg-white border rounded-pill px-3 py-1 shadow-sm mt-2 mt-lg-0">
            <div class="rounded-circle d-flex align-items-center justify-content-center fw-bold me-2" style="width: 25px; height: 25px; background-color: #FFE6F0; color: #E91E63; font-size: 11px;">L</div>
            <span class="fw-bold text-dark" style="font-size: 13px;">Login</span>
        </a>
<?php endif; ?>';

    // Replacement 2 (Index.php style)
    $rep2 = '<?php if ($is_logged_in): ?>
          <div class="dropdown">
            <div class="d-flex align-items-center gap-2" data-bs-toggle="dropdown" style="cursor: pointer;">
              <img src="asset/img/icon-female.png" alt="Profile" class="rounded-circle border" width="40" height="40" style="object-fit: cover; border-color: #EBE6E9 !important;" />
              <div class="d-none d-md-block">
                <div class="fw-bold text-dark" style="font-size: 14px"><?= htmlspecialchars($user_nama) ?></div>
                <div class="text-muted text-capitalize" style="font-size: 12px"><?= htmlspecialchars($_SESSION["user_role"] ?? "Pasien") ?></div>
              </div>
            </div>
            <ul class="dropdown-menu dropdown-menu-end shadow border-0 mt-2" style="border-radius: 12px; font-size: 14px;">
              <li><a class="dropdown-item py-2" href="#"><i class="fa-solid fa-user me-2 text-muted"></i> Profile</a></li>
              <li><a class="dropdown-item py-2" href="#"><i class="fa-solid fa-gear me-2 text-muted"></i> Setting</a></li>
              <li><a class="dropdown-item py-2" href="<?= $dashboard_url ?>"><i class="fa-solid fa-chart-line me-2 text-muted"></i> Dashboard</a></li>
              <li><hr class="dropdown-divider"></li>
              <li><a class="dropdown-item py-2 text-danger fw-bold" href="config/function_auth.php?action=logout"><i class="fa-solid fa-right-from-bracket me-2"></i> Logout</a></li>
            </ul>
          </div>
<?php else: ?>
          <div>
            <a href="login.php" class="btn btn-primary-custom fw-bold rounded-pill px-4">Login</a>
          </div>
<?php endif; ?>';

    $content = preg_replace($pattern1, $rep1, $content);
    $content = preg_replace($pattern2, $rep2, $content);

    // Optional: fix the broken simple ones that were replaced by my first script (if any)
    $pattern_broken = '/<a href="<\?= \$dashboard_url \?>" class="text-decoration-none">\s*<div class="d-flex align-items-center border rounded-pill[^>]*>.*?<\/a>/is';
    $content = preg_replace($pattern_broken, $rep1, $content);

    file_put_contents($file, $content);
    echo "Processed $file\n";
}
?>
