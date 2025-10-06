</main>
    </div>
    
    <style>
      /* 侧栏遮罩层，移动端用于点击背景收起 */
      .sidebar-overlay {
        position: fixed;
        inset: 0;
        background: rgba(0,0,0,0.35);
        z-index: 1050;
        display: none;
      }
      .sidebar-open .sidebar-overlay { display: block; }
    </style>
    <div class="sidebar-overlay" aria-hidden="true"></div>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var toggleBtn = document.querySelector('.sidebar-toggle');
            var overlay = document.querySelector('.sidebar-overlay');
            function isMobile() { return window.innerWidth < 992; }
            function updateOverlay() {
              if (isMobile() && !document.body.classList.contains('sidebar-collapsed')) {
                document.body.classList.add('sidebar-open');
              } else {
                document.body.classList.remove('sidebar-open');
              }
            }
            if (toggleBtn) {
                toggleBtn.addEventListener('click', function() {
                    document.body.classList.toggle('sidebar-collapsed');
                    updateOverlay();
                });
            }
            if (overlay) {
              overlay.addEventListener('click', function() {
                document.body.classList.add('sidebar-collapsed');
                updateOverlay();
              });
            }
            // 根据窗口宽度初始化折叠状态
            function initSidebarState() {
                if (window.innerWidth < 992) {
                    document.body.classList.add('sidebar-collapsed');
                } else {
                    document.body.classList.remove('sidebar-collapsed');
                }
                updateOverlay();
            }
            initSidebarState();
            window.addEventListener('resize', initSidebarState);
        });
    </script>
</body>
</html>