<?php
// Minimal sidebar to avoid WP deprecation notice.
if (!defined('ABSPATH')) { exit; }
?>
<aside class="sidebar" role="complementary">
  <?php if (is_active_sidebar('sidebar-1')) { dynamic_sidebar('sidebar-1'); } ?>
</aside>
