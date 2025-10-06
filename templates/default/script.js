/**
 * PHP专业博客程序 - JavaScript交互文件
 */

// DOM元素加载完成后执行
document.addEventListener('DOMContentLoaded', function() {
    // 初始化返回顶部按钮
    initBackToTopButton();
    
    // 初始化导航栏滚动效果
    initNavbarScroll();
    
    // 初始化评论表单提交
    initCommentForm();
    
    // 初始化文章卡片悬停效果
    initPostHoverEffects();
    
    // 初始化平滑滚动
    initSmoothScroll();
});

/**
 * 初始化返回顶部按钮
 */
function initBackToTopButton() {
    const backToTopButton = document.getElementById('back-to-top');
    
    if (backToTopButton) {
        // 滚动监听
        window.addEventListener('scroll', function() {
            if (window.pageYOffset > 300) {
                backToTopButton.classList.add('visible');
            } else {
                backToTopButton.classList.remove('visible');
            }
        });
        
        // 点击事件
        backToTopButton.addEventListener('click', function(e) {
            e.preventDefault();
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        });
    }
}

/**
 * 初始化导航栏滚动效果
 */
function initNavbarScroll() {
    const navbar = document.querySelector('.blog-nav');
    let lastScrollTop = 0;
    
    if (navbar) {
        window.addEventListener('scroll', function() {
            const scrollTop = window.pageYOffset || document.documentElement.scrollTop;
            
            if (scrollTop > 100) {
                navbar.classList.add('scrolled');
            } else {
                navbar.classList.remove('scrolled');
            }
            
            lastScrollTop = scrollTop;
        });
    }
}

/**
 * 初始化评论表单提交
 */
function initCommentForm() {
    const commentForm = document.querySelector('.comment-form form');
    
    if (commentForm) {
        commentForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            // 获取表单数据
            const name = document.getElementById('name').value;
            const email = document.getElementById('email').value;
            const content = document.getElementById('content').value;
            
            // 表单验证
            if (!name || !email || !content) {
                alert('请填写完整的评论信息');
                return;
            }
            
            // 模拟提交
            const submitButton = commentForm.querySelector('button[type="submit"]');
            const originalText = submitButton.innerHTML;
            
            submitButton.disabled = true;
            submitButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i> 提交中...';
            
            // 模拟AJAX请求延迟
            setTimeout(function() {
                // 重置表单
                commentForm.reset();
                
                // 恢复按钮状态
                submitButton.disabled = false;
                submitButton.innerHTML = originalText;
                
                // 显示成功提示
                alert('评论提交成功，等待审核！');
            }, 1500);
        });
    }
}

/**
 * 初始化文章卡片悬停效果
 */
function initPostHoverEffects() {
    const posts = document.querySelectorAll('.blog-post');
    
    posts.forEach(post => {
        // 鼠标进入事件
        post.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-5px)';
            this.style.boxShadow = '0 5px 20px rgba(0, 0, 0, 0.1)';
        });
        
        // 鼠标离开事件
        post.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0)';
            this.style.boxShadow = '0 2px 10px rgba(0, 0, 0, 0.05)';
        });
    });
}

/**
 * 初始化平滑滚动
 */
function initSmoothScroll() {
    const links = document.querySelectorAll('a[href^="#"]');
    
    links.forEach(link => {
        link.addEventListener('click', function(e) {
            // 只处理页面内锚点链接
            if (this.getAttribute('href') === '#') {
                e.preventDefault();
                return;
            }
            
            // 检查是否是页面内锚点链接
            const href = this.getAttribute('href');
            if (href.startsWith('#') && href.length > 1) {
                e.preventDefault();
                
                const targetId = href.substring(1);
                const targetElement = document.getElementById(targetId);
                
                if (targetElement) {
                    targetElement.scrollIntoView({
                        behavior: 'smooth'
                    });
                }
            }
        });
    });
}

/**
 * 搜索功能增强
 */
function enhanceSearchFunction() {
    const searchForm = document.querySelector('.search-form form');
    const searchInput = searchForm.querySelector('input[type="text"]');
    
    if (searchInput) {
        // 搜索输入框获得焦点时的效果
        searchInput.addEventListener('focus', function() {
            this.parentElement.classList.add('focused');
        });
        
        // 搜索输入框失去焦点时的效果
        searchInput.addEventListener('blur', function() {
            this.parentElement.classList.remove('focused');
        });
        
        // 搜索表单提交
        searchForm.addEventListener('submit', function(e) {
            const keyword = searchInput.value.trim();
            if (!keyword) {
                e.preventDefault();
                alert('请输入搜索关键词');
            }
        });
    }
}

/**
 * 添加加载动画效果
 */
function addLoadingAnimation() {
    // 创建加载指示器
    const createLoader = function() {
        const loader = document.createElement('div');
        loader.className = 'loader';
        loader.innerHTML = '<div class="spinner"></div><span>加载中...</span>';
        return loader;
    };
    
    // 为分页链接添加加载效果
    const paginationLinks = document.querySelectorAll('.pagination a');
    paginationLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            
            // 在内容区域显示加载指示器
            const mainContent = document.querySelector('.main-content');
            if (mainContent) {
                // 保存当前内容
                const currentContent = mainContent.innerHTML;
                
                // 显示加载指示器
                mainContent.innerHTML = '';
                mainContent.appendChild(createLoader());
                
                // 模拟加载延迟
                setTimeout(function() {
                    // 恢复内容（实际应用中应该加载新内容）
                    mainContent.innerHTML = currentContent;
                }, 1500);
            }
        });
    });
}

/**
 * 初始化统计图表
 */
function initStatsCharts() {
    // 检查是否在关于页面
    if (document.querySelector('.about-page')) {
        // 这里可以添加统计图表的初始化代码
        // 使用Chart.js创建统计图表
    }
}

/**
 * 响应式菜单切换
 */
function initResponsiveMenu() {
    // 在小屏幕设备上添加菜单切换功能
    const menuToggle = document.createElement('button');
    menuToggle.className = 'menu-toggle';
    menuToggle.innerHTML = '<i class="fas fa-bars"></i>';
    menuToggle.style.display = 'none'; // 默认隐藏
    
    const navbar = document.querySelector('.blog-nav');
    if (navbar) {
        navbar.appendChild(menuToggle);
        
        // 监听窗口大小变化
        window.addEventListener('resize', function() {
            const navMenu = document.querySelector('.nav-menu');
            if (window.innerWidth < 768) {
                menuToggle.style.display = 'block';
                navMenu.style.display = 'none';
            } else {
                menuToggle.style.display = 'none';
                navMenu.style.display = 'flex';
            }
        });
        
        // 点击菜单按钮切换菜单显示
        menuToggle.addEventListener('click', function() {
            const navMenu = document.querySelector('.nav-menu');
            if (navMenu.style.display === 'none' || navMenu.style.display === '') {
                navMenu.style.display = 'flex';
                menuToggle.innerHTML = '<i class="fas fa-times"></i>';
            } else {
                navMenu.style.display = 'none';
                menuToggle.innerHTML = '<i class="fas fa-bars"></i>';
            }
        });
        
        // 初始化菜单状态
        if (window.innerWidth < 768) {
            const navMenu = document.querySelector('.nav-menu');
            navMenu.style.display = 'none';
        }
    }
}

// 额外初始化函数调用
setTimeout(function() {
    enhanceSearchFunction();
    addLoadingAnimation();
    initStatsCharts();
    initResponsiveMenu();
}, 500);