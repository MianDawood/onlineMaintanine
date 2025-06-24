<style>
.menu-item-flex {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.menu-item-flex a {
  flex-grow: 1;
  text-decoration: none;
  color: #333;
}

.bookmark-icon {
  margin-left: 10px;
  cursor: pointer;
  color: #999;
  width: 20px;
  text-align: right;
}

.bookmark-icon.active {
  color: orange;
}
.menu-item-flex {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 8px 12px;
  gap: 16px; /* <== Adds space between text and star */
  white-space: nowrap;
}

</style>
<?php
/**
 * @copyright Copyright 2003-2024 Zen Cart Development Team
 * @copyright Portions Copyright 2003 osCommerce
 * @license http://www.zen-cart.com/license/2_0.txt GNU Public License V2.0
 * @version $Id: Nick Fenwick 2023 Jul 03 Modified in v2.0.0-alpha1 $
 */

if (!defined('IS_ADMIN_FLAG')) die('Illegal Access');

$menuTitles = zen_get_menu_titles();
?>
<nav class="navbar navbar-default">
  <!-- Brand and toggle get grouped for better mobile display -->
  <div class="navbar-header">
    <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-adm1-collapse">
      <span class="sr-only">Toggle navigation</span>
      <span class="icon-bar">&nbsp;</span>
      <span class="icon-bar">&nbsp;</span>
      <span class="icon-bar">&nbsp;</span>
    </button>
  </div>
  <!-- Collect the nav links, forms, and other content for toggling -->
  <div class="collapse navbar-collapse navbar-adm1-collapse">
    <ul class="nav navbar-nav">
                 <!-- <a href="allreviews.php">All Reviews</a> -->

          <?php foreach (zen_get_admin_menu_for_user() as $menuKey => $pages) { ?>
            <li class="dropdown">

              <a href="<?php echo zen_href_link(FILENAME_ALT_NAV) ?>" class="dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true"><?php echo $menuTitles[$menuKey] ?><b class="caret">&nbsp;</b></a>
              <ul class="dropdown-menu">
                <?php foreach ($pages as $page) { ?>
              
                  <li>
  <div class="menu-item-flex">
    <a href="<?php echo zen_href_link($page['file'], $page['params']) ?>">
      <?php echo $page['name'] ?>
    </a>
    <i class="fa fa-star-o bookmark-icon" data-menu-id="<?php echo md5($page['file'] . $page['params']); ?>"></i>
  </div>
</li>
                  
                <?php } ?>
                
              </ul>
            </li>
          <?php } ?>
          <li class="upperMenuItems"><a href="<?php echo zen_href_link(FILENAME_DEFAULT, '', 'NONSSL'); ?>" class="headerLink"><?php echo HEADER_TITLE_TOP; ?></a></li>
          <li class="upperMenuItems"><a href="<?php echo zen_catalog_href_link(FILENAME_DEFAULT); ?>" class="headerLink" rel="noopener" target="_blank"><?php echo HEADER_TITLE_ONLINE_CATALOG; ?></a></li>
          <li class="upperMenuItems"><a href="https://www.zen-cart.com/forum" class="headerLink" rel="noopener" target="_blank"><?php echo HEADER_TITLE_SUPPORT_SITE; ?></a></li>
          <li class="upperMenuItems"><a href="<?php echo zen_href_link(FILENAME_SERVER_INFO, '', 'NONSSL'); ?>" class="headerLink"><?php echo HEADER_TITLE_VERSION; ?></a></li>
          <li class="upperMenuItems"><a href="<?php echo zen_href_link(FILENAME_ADMIN_ACCOUNT, '', 'NONSSL'); ?>" class="headerLink"><?php echo HEADER_TITLE_ACCOUNT; ?></a></li>
          <li class="upperMenuItems"><a href="<?php echo zen_href_link(FILENAME_LOGOFF, '', 'NONSSL'); ?>" class="headerLink"><?php echo HEADER_TITLE_LOGOFF; ?></a></li>
    </ul>
  </div><!-- /.navbar-collapse -->
</nav>
<?php if ($url = page_has_help()) { ?>
<div class="pull-right noprint">
  <a href="<?php echo $url; ?>" rel="noopener" target="_blank" class="btn btn-sm btn-default btn-help" role="button" title="Help">
    <i class="fa-regular fa-question fa-lg" aria-hidden="true"></i>
  </a>
</div>
<?php } ?>

<script>
  document.addEventListener('DOMContentLoaded', function () {
    const savedBookmarks = JSON.parse(localStorage.getItem('adminBookmarks') || '{}');

    document.querySelectorAll('.bookmark-icon').forEach(function (icon) {
      const id = icon.getAttribute('data-menu-id');

      if (savedBookmarks[id]) {
        icon.classList.add('active');
        icon.classList.remove('fa-star-o');
        icon.classList.add('fa-star');
      }

      icon.addEventListener('click', function () {
        icon.classList.toggle('active');
        const isActive = icon.classList.contains('active');
        icon.classList.toggle('fa-star-o', !isActive);
        icon.classList.toggle('fa-star', isActive);

        savedBookmarks[id] = isActive;
        localStorage.setItem('adminBookmarks', JSON.stringify(savedBookmarks));
      });
    });
  });
</script>

