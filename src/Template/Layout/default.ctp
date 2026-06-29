<!DOCTYPE html>

<html>
<head>
    <?= $this->Html->charset() ?>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Page with empty content"/>
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no"/>
    <title>
        Meta - <?= $this->fetch('title') ?>
    </title>
    <?= $this->Html->meta('icon') ?>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Poppins:300,400,500,600,700"/>
    <?= $this->Html->css('/assets/plugins/custom/fullcalendar/fullcalendar.bundle.css') ?>
    <?= $this->Html->css('/assets/plugins/global/plugins.bundle.css') ?>
    <?= $this->Html->css('/assets/plugins/custom/prismjs/prismjs.bundle.css') ?>
    <?= $this->Html->css('/assets/css/style.bundle.css') ?>
    <?= $this->Html->css('/assets/css/themes/layout/header/base/dark.css') ?>
    <?= $this->Html->css('/assets/css/themes/layout/header/menu/dark.css') ?>
    <?= $this->Html->css('/assets/css/style.bundle.css') ?>
    <?= $this->Html->css('/assets/css/themes/layout/brand/dark.css') ?>
    <?= $this->Html->css('/assets/css/themes/layout/aside/dark.css') ?>

    <!-- DataTables CSS -->
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.21/css/dataTables.bootstrap4.min.css"/>
    <?= $this->Html->css('/css/dashboard-custom.css') ?>
    
    <style>
        :root {
            --primary-color: #667eea;
            --primary-dark: #764ba2;
            --sidebar-bg: #2c3e50;
            --sidebar-text: #ecf0f1;
            --border-color: #ecf0f1;
            --light-bg: #f8f9fa;
        }
        
        body {
            background-color: var(--light-bg);
            color: #333;
        }
        
        .aside {
            background: linear-gradient(180deg, var(--sidebar-bg) 0%, #34495e 100%);
            border-right: 1px solid rgba(255,255,255,0.1);
            box-shadow: 2px 0 10px rgba(0,0,0,0.05);
        }
        
        .brand {
            border-bottom: 1px solid rgba(255,255,255,0.1);
            padding: 20px 15px;
            background: rgba(0,0,0,0.2);
        }
        
        .aside-menu .menu-item > a {
            color: var(--sidebar-text);
            padding: 12px 20px;
            border-radius: 6px;
            margin: 4px 10px;
            transition: all 0.3s ease;
            font-weight: 500;
            font-size: 14px;
        }
        
        .aside-menu .menu-item > a:hover {
            background: rgba(102, 126, 234, 0.15);
            color: #667eea;
            padding-left: 24px;
        }
        
        .aside-menu .menu-item.menu-item-active > a {
            background: linear-gradient(90deg, var(--primary-color), var(--primary-dark));
            color: white;
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
        }
        
        .aside-menu .menu-item-submenu > a::after {
            content: '';
            position: absolute;
            right: 20px;
            width: 20px;
            height: 20px;
            background: no-repeat center;
            transition: transform 0.3s ease;
        }
        
        .wrapper {
            background-color: var(--light-bg);
        }
        
        .content {
            background-color: var(--light-bg);
        }
        
        .container {
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.08);
            padding: 30px !important;
            margin-bottom: 20px;
        }
        
        .header {
            background: white;
            border-bottom: 1px solid var(--border-color);
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
            padding: 15px 0;
        }
        
        .header-mobile {
            background: linear-gradient(90deg, var(--sidebar-bg), #34495e);
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }
        
        .scrolltop {
            background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));
            border-radius: 50%;
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
        }
        
        .scrolltop:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(102, 126, 234, 0.5);
        }
    </style>
    
    <?= $this->fetch('meta') ?>
    <?= $this->fetch('css_top') ?>
    <?= $this->fetch('script_top') ?>

</head>
<body id="kt_body" class="page-loading-enabled page-loading header-static header-mobile-fixed subheader-enabled aside-enabled aside-fixed aside-minimize-hoverable page-loading">
    <div id="kt_header_mobile" class="header-mobile align-items-center  header-mobile-fixed " >
        <a href="<?= $this->Url->build('/'); ?>">
            <?= $this->Html->image('/logo-light.png') ?>
        </a>
        <div class="d-flex align-items-center">
            <button class="btn p-0 burger-icon burger-icon-left" id="kt_aside_mobile_toggle">
                <span>
                </span>
            </button>
            <button class="btn p-0 burger-icon ml-4" id="kt_header_mobile_toggle">
                    <span></span>
                </button>
            <button class="btn btn-hover-text-primary p-0 ml-2" id="kt_header_mobile_topbar_toggle">
                <span class="svg-icon svg-icon-xl">
                    <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                        <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                            <polygon points="0 0 24 0 24 24 0 24"/>
                            <path d="M12,11 C9.790861,11 8,9.209139 8,7 C8,4.790861 9.790861,3 12,3 C14.209139,3 16,4.790861 16,7 C16,9.209139 14.209139,11 12,11 Z" fill="#000000" fill-rule="nonzero" opacity="0.3"/>
                            <path d="M3.00065168,20.1992055 C3.38825852,15.4265159 7.26191235,13 11.9833413,13 C16.7712164,13 20.7048837,15.2931929 20.9979143,20.2 C21.0095879,20.3954741 20.9979143,21 20.2466999,21 C16.541124,21 11.0347247,21 3.72750223,21 C3.47671215,21 2.97953825,20.45918 3.00065168,20.1992055 Z" fill="#000000" fill-rule="nonzero"/>
                        </g>
                    </svg>
                </span>       
            </button>
        </div>
    </div>

    <div class="d-flex flex-column flex-root">
        <div class="d-flex flex-row flex-column-fluid page">
            <div class="aside aside-left  aside-fixed  d-flex flex-column flex-row-auto"  id="kt_aside">
                <div class="brand flex-column-auto " id="kt_brand">
                    <a href="<?= $this->Url->build('/'); ?>" class="brand-logo">
                        <?= $this->Html->image('/logo-light.png') ?>
                    </a>
                    <button class="brand-toggle btn btn-sm px-0" id="kt_aside_toggle">
                        <span class="svg-icon svg-icon svg-icon-xl">
                            <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                    <polygon points="0 0 24 0 24 24 0 24"/>
                                    <path d="M5.29288961,6.70710318 C4.90236532,6.31657888 4.90236532,5.68341391 5.29288961,5.29288961 C5.68341391,4.90236532 6.31657888,4.90236532 6.70710318,5.29288961 L12.7071032,11.2928896 C13.0856821,11.6714686 13.0989277,12.281055 12.7371505,12.675721 L7.23715054,18.675721 C6.86395813,19.08284 6.23139076,19.1103429 5.82427177,18.7371505 C5.41715278,18.3639581 5.38964985,17.7313908 5.76284226,17.3242718 L10.6158586,12.0300721 L5.29288961,6.70710318 Z" fill="#000000" fill-rule="nonzero" transform="translate(8.999997, 11.999999) scale(-1, 1) translate(-8.999997, -11.999999) "/>
                                    <path d="M10.7071009,15.7071068 C10.3165766,16.0976311 9.68341162,16.0976311 9.29288733,15.7071068 C8.90236304,15.3165825 8.90236304,14.6834175 9.29288733,14.2928932 L15.2928873,8.29289322 C15.6714663,7.91431428 16.2810527,7.90106866 16.6757187,8.26284586 L22.6757187,13.7628459 C23.0828377,14.1360383 23.1103407,14.7686056 22.7371482,15.1757246 C22.3639558,15.5828436 21.7313885,15.6103465 21.3242695,15.2371541 L16.0300699,10.3841378 L10.7071009,15.7071068 Z" fill="#000000" fill-rule="nonzero" opacity="0.3" transform="translate(15.999997, 11.999999) scale(-1, 1) rotate(-270.000000) translate(-15.999997, -11.999999) "/>
                                </g>
                            </svg>
                        </span>           
                    </button>
                </div>
                <?= $this->element('general/asidemenu') ?>
            </div>
        </div>
        <div class="d-flex flex-column flex-row-fluid wrapper" id="kt_wrapper">
            <?= $this->element('general/header') ?>
            <div class="content  d-flex flex-column flex-column-fluid" id="kt_content">
                <?= $this->element('general/subheader')  ?>
                <div class="d-flex flex-column-fluid">
                    <div class=" container ">
                        <?= $this->Flash->render() ?>
                        <?= $this->fetch('content')  ?>
                    </div>
                </div>
            </div>
            <?= $this->element('general/footer') ?>
        </div>
    </div>
</div>
<?= $this->element('general/quickuser')  ?>


<div id="kt_scrolltop" class="scrolltop">
    <span class="svg-icon">
        <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
            <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                <polygon points="0 0 24 0 24 24 0 24"/>
                <rect fill="#000000" opacity="0.3" x="11" y="10" width="2" height="10" rx="1"/>
                <path d="M6.70710678,12.7071068 C6.31658249,13.0976311 5.68341751,13.0976311 5.29289322,12.7071068 C4.90236893,12.3165825 4.90236893,11.6834175 5.29289322,11.2928932 L11.2928932,5.29289322 C11.6714722,4.91431428 12.2810586,4.90106866 12.6757246,5.26284586 L18.6757246,10.7628459 C19.0828436,11.1360383 19.1103465,11.7686056 18.7371541,12.1757246 C18.3639617,12.5828436 17.7313944,12.6103465 17.3242754,12.2371541 L12.0300757,7.38413782 L6.70710678,12.7071068 Z" fill="#000000" fill-rule="nonzero"/>
            </g>
        </svg>
    </span>
</div>
<div id="empModal" class="modal fade" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-xl">
                <div class="modal-content">
                </div>
            </div>
        </div>
<?= $this->Html->script('/assets/plugins/global/plugins.bundle.js') ?>
<?= $this->Html->script('/assets/plugins/custom/prismjs/prismjs.bundle.js') ?>

<!-- DataTables JS -->
<script type="text/javascript" src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/1.10.21/js/dataTables.bootstrap4.min.js"></script>
<!-- DataTables Select extension (optional, but used in products.js) -->
<script type="text/javascript" src="https://cdn.datatables.net/select/1.3.1/js/dataTables.select.min.js"></script>
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/select/1.3.1/css/select.dataTables.min.css"/>


<?= $this->Html->script('/assets/js/scripts.bundle.js') ?>
<?= $this->fetch('css_bottom') ?>
<?= $this->fetch('script_bottom') ?>
<?= $this->fetch('script') ?>
<?= $this->Html->scriptStart() ?>
    function goBack() {
      window.history.back();
    }
    var HURL = "<?= $this->Url->build('/'); ?>";
    $(".defaultwh").click(function(){
        var defaultwh = $(this).data('id');
        $.ajax({
            url: HURL+"orders/defaultwh/"+defaultwh,
            success: function(response){
                defaultwhtype=<?= $this->request->getSession()->read('Auth.User.defaultwhtype'); ?>;
                if(defaultwhtype==1){
                    location.reload();
                }else{
                    location.replace(HURL+'packs');
                }
            }
        });
    });
    $( "#kt_form_1" ).submit(function( event ) {
        $(".btn").attr("disabled", true);
    });
<?= $this->Html->scriptEnd(); ?>
<?= $this->Html->scriptStart() ?>
			var KTAppSettings = {
				"breakpoints": {
					"sm": 576,
					"md": 768,
					"lg": 992,
					"xl": 1200,
					"xxl": 1400
				},
				"colors": {
					"theme": {
						"base": {
							"white": "#ffffff",
							"primary": "#3699FF",
							"secondary": "#E5EAEE",
							"success": "#1BC5BD",
							"info": "#8950FC",
							"warning": "#FFA800",
							"danger": "#F64E60",
							"light": "#E4E6EF",
							"dark": "#181C32"
						},
						"light": {
							"white": "#ffffff",
							"primary": "#E1F0FF",
							"secondary": "#EBEDF3",
							"success": "#C9F7F5",
							"info": "#EEE5FF",
							"warning": "#FFF4DE",
							"danger": "#FFE2E5",
							"light": "#F3F6F9",
							"dark": "#D6D6E0"
						},
						"inverse": {
							"white": "#ffffff",
							"primary": "#ffffff",
							"secondary": "#3F4254",
							"success": "#ffffff",
							"info": "#ffffff",
							"warning": "#ffffff",
							"danger": "#ffffff",
							"light": "#464E5F",
							"dark": "#ffffff"
						}
					},
					"gray": {
						"gray-100": "#F3F6F9",
						"gray-200": "#EBEDF3",
						"gray-300": "#E4E6EF",
						"gray-400": "#D1D3E0",
						"gray-500": "#B5B5C3",
						"gray-600": "#7E8299",
						"gray-700": "#5E6278",
						"gray-800": "#3F4254",
						"gray-900": "#181C32"
					}
				},
				"font-family": "Poppins"
			};

    $(function() {
        // this will get the full URL at the address bar
        var url = window.location.href;

        // passes on every "a" tag
        $(".aside-menu-wrapper a").each(function() {
            // checks if its the same on the address bar
            if (url == (this.href)) {
                $(this).closest("li").addClass("menu-item-active");
                //for making parent of submenu active
               $(this).closest("li").parent().parent().parent().addClass("menu-item menu-item-submenu menu-item-open");
               $(this).closest("li").parent().parent().parent().parent().parent().parent().addClass("menu-item menu-item-submenu menu-item-open");
            }
        });
    });        
<?= $this->Html->scriptEnd(); ?>
</body>
<style type="text/css">
    h4.menu-text {
        font-weight: 900 !important;
        font-size: 13px !important;
        color: white !important;
    }
    
    /* Enhanced styling for menu items */
    .aside-menu-wrapper {
        padding: 15px 0;
    }
    
    .aside-menu .menu-item-submenu > .menu-link {
        background: rgba(255,255,255,0.05) !important;
        margin: 4px 10px;
        border-radius: 6px;
        padding-left: 20px;
    }
    
    .aside-menu .menu-item-submenu > .menu-link:hover {
        background: rgba(102, 126, 234, 0.15) !important;
        color: #667eea !important;
    }
    
    /* Submenu styling */
    .aside-menu .menu-submenu {
        background: rgba(0,0,0,0.1);
        margin: 8px 0;
        border-radius: 6px;
    }
    
    .aside-menu .menu-submenu .menu-link {
        padding-left: 40px !important;
        font-size: 13px;
        color: rgba(236, 240, 241, 0.8);
    }
    
    .aside-menu .menu-submenu .menu-link:hover {
        background: rgba(102, 126, 234, 0.2) !important;
        color: #667eea !important;
    }
    
    /* Table styling */
    .table {
        background: white;
        border-radius: 8px;
        overflow: hidden;
    }
    
    .table thead {
        background: linear-gradient(90deg, #f8f9fa, #ffffff);
        border-bottom: 2px solid #e9ecef;
    }
    
    .table tbody tr:hover {
        background-color: rgba(102, 126, 234, 0.05);
    }
</style>
</html>
