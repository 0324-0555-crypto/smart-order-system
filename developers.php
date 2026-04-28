<?php
session_start();
require_once 'classes/Auth.php';
Auth::check();
$pageTitle = 'Developers';
include 'includes/header.php';
?>

<div class="page-header">
    <div class="page-header-left">
        <h2>Development Team</h2>
        <p>The people behind the Smart Order Management System.</p>
    </div>
</div>

<div class="content-card mb-4">
    <div class="card-header-custom">
        <h5><i class="bi bi-code-slash me-2" style="color:var(--electric);"></i>Group Members</h5>
        <small style="color:#718096;">ITEL 203 &mdash; Section 2C &mdash; LSPU San Pablo City Campus</small>
    </div>
    <div class="card-body-custom">
        <div class="row g-4 justify-content-center">

            <div class="col-md-4">
                <div class="profile-card">
                    <div class="profile-avatar">M1</div>
                    <div class="profile-name">Carl Arancel</div>
                    <div class="profile-role mb-2">Lead Developer</div>
                    <div style="font-size:12px; color:#718096; margin-bottom:12px;">
                        Database Design, Backend PHP, OOP Implementation
                    </div>
                    <div>
                        <span class="tech-pill">PHP</span>
                        <span class="tech-pill green">MySQL</span>
                        <span class="tech-pill navy">PDO</span>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="profile-card">
                    <div class="profile-avatar" style="background:linear-gradient(135deg,var(--accent),var(--accent-dim));">M2</div>
                    <div class="profile-name">Stanley Becina</div>
                    <div class="profile-role mb-2" style="color:var(--accent);">Frontend Developer</div>
                    <div style="font-size:12px; color:#718096; margin-bottom:12px;">
                        UI Design, Bootstrap Layout, CSS Styling
                    </div>
                    <div>
                        <span class="tech-pill">HTML</span>
                        <span class="tech-pill navy">CSS</span>
                        <span class="tech-pill orange">Bootstrap</span>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="profile-card">
                    <div class="profile-avatar" style="background:linear-gradient(135deg,#a855f7,#7c3aed);">M3</div>
                    <div class="profile-name">Axle Cepillo</div>
                    <div class="profile-role mb-2" style="color:#7c3aed;">Quality Assurance</div>
                    <div style="font-size:12px; color:#718096; margin-bottom:12px;">
                        Testing, Documentation, Deployment
                    </div>
                    <div>
                        <span class="tech-pill">GitHub</span>
                        <span class="tech-pill green">InfinityFree</span>
                        <span class="tech-pill orange">XAMPP</span>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<!-- Tech Stack Used -->
<div class="content-card">
    <div class="card-header-custom">
        <h5><i class="bi bi-stack me-2" style="color:var(--electric);"></i>Technology Stack</h5>
    </div>
    <div class="card-body-custom">
        <div class="row g-3">
            <?php
            $stack = [
                ['PHP 8',         'bi-filetype-php',    'var(--electric)',  'Server-side scripting, OOP classes, session handling'],
                ['MySQL',         'bi-database-fill',   'var(--accent)',    'Relational database, foreign keys, JOIN queries'],
                ['Bootstrap 5',   'bi-bootstrap-fill',  '#7c3aed',          'Responsive UI components and grid layout'],
                ['HTML5 & CSS3',  'bi-code-slash',      '#e25822',          'Page structure and custom stylesheet'],
                ['JavaScript',    'bi-lightning-charge','#f59e0b',          'Client-side interactions and sidebar toggle'],
                ['XAMPP',         'bi-server',          'var(--navy)',       'Local development server (Apache + MySQL)'],
                ['GitHub',        'bi-github',          '#24292e',          'Version control and team collaboration'],
                ['InfinityFree',  'bi-cloud-upload',    '#1e90ff',          'Free web hosting for live deployment'],
            ];
            foreach ($stack as $item): ?>
            <div class="col-md-3 col-6">
                <div style="text-align:center; padding:18px 12px; border:1px solid #e2e8f0; border-radius:10px; background:#fafcff;">
                    <i class="bi <?= $item[1] ?>" style="font-size:28px; color:<?= $item[2] ?>; margin-bottom:10px; display:block;"></i>
                    <div style="font-weight:700; font-size:13px; color:var(--navy); margin-bottom:4px;"><?= $item[0] ?></div>
                    <div style="font-size:11px; color:#a0aec0;"><?= $item[3] ?></div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
