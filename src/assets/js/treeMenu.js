/**
 *  //1、menu01: JS Array
 *  var menus01 = [{
 *      name: "menu1",
 *      href: "",
 *      subMenus: [{
 *          name: "menu1.1",
 *          href: "",
 *          subMenus: [{
 *              name: "menu1.1.1",
 *              href: "#menu1.1.1",
 *          }, {
 *              name: "menu1.1.2",
 *              href: "#menu1.1.2"
 *          }]
 *      },
 *      ....//省略若干子菜单
 *      ]
 *  },
 *  ...//省略若干菜单
 *  ];
 *
 *  //2、menu02：JSON Array
 *  var menus02 = JSON.stringify(menus01);
 *
 *  //3、menu03: JS Object
 *  var menus03 = {
 *      name: "menu1",
 *      href: "",
 *      subMenus: [{
 *          name: "menu1.1",
 *          href: "",
 *          subMenus: [{
 *              name: "menu1.1.1",
 *              href: "#menu1.1.1",
 *          }, {
 *              name: "menu1.1.2",
 *              href: "#menu1.1.2"
 *          }]
 *      }, {
 *          name: "menu1.2",
 *          href: "#menu1.2",
 *          subMenus: [{
 *              name: "menu1.2.1",
 *              href: "#menu1.2.1"
 *          }, {
 *              name: "menu1.2.2",
 *              href: "#menu1.2.2"
 *          }]
 *      }, {
 *          name: "menu1.3",
 *          href: "#menu1.3",
 *          subMenus: [{
 *              name: "menu1.3.1",
 *              href: "#menu1.3.1"
 *          }, {
 *              name: "menu1.3.2",
 *              href: "#menu1.3.2"
 *          }]
 *      }]
 *  };
 *
 *  //4、JSON object
 *  var menus04 = JSON.stringify(menus03);
 *
 *  var config01 = {
 *      treeMenuId: "#TreeMenu01",  //required
 *      superLevel: 0,              //optional
 *      multiple: true,             //optional
 *  };
 *
 *  var config02 = {
 *      treeMenuId: "#TreeMenu02",  //required
 *      superLevel: 0,              //optional
 *      multiple: false             //optional
 *  };
 *
 *  var config03 = {
 *      treeMenuId: "#TreeMenu03",  //required
 *      superLevel: 0,              //optional
 *      multiple: true              //optional
 *  }
 *
 *  var config04 = {
 *      treeMenuId: "#TreeMenu04",   //required
 *      superLevel: 0,               //optional
 *      multiple: false              //optional
 *  }
 *  treeMenu.init(menus01, config01);
 *  treeMenu.init(menus02, config02);
 *  treeMenu.init(menus03, config03);
 *  treeMenu.init(menus04, config04);
 *
 *  @author DarkRanger
 *  see more https://coding.net/u/wrcold520/p/TreeMenu-Simple-Tree-Menu-js/git/tree/master/TreeMenu
 *
 */
! function($, JSON, checkutil) {
    "use strict";

    var treeMenu = {};
    var menuConfig = {};

    treeMenu.name = "MenuTree-Simple Left Navigation Tree Menu";
    treeMenu.version = "version-1.0";
    treeMenu.author = "DarkRanger";
    treeMenu.email = "15934179313@163.com";
    treeMenu.url = "https://coding.net/u/wrcold520/p/TreeMenu-Simple-Tree-Menu-js/git/tree/master/TreeMenu";

    treeMenu.config = function() {};

    treeMenu.init = function(datas, config) {
        menuConfig = new treeMenu.config();
        if(checkutil.isUndefined(datas)) {
            console.log("The function treeMenu.init() doesn't have the parameter 'datas' that means the treeMenu has no Datas!");
            return;
        } else {
            //JS Object or JS Array
            if(checkutil.isArray(datas)) {
                menuConfig.menus = datas;
            } else if(checkutil.isObject(datas)) {
                menuConfig.menus = [datas];
            }
            //JSONString
            else if(checkutil.isString(datas)) {
                var menuObj;
                try {
                    var menuJson = JSON.parse(datas);
                    if(checkutil.isArray(menuJson)) {
                        menuObj = menuJson;
                    } else if(checkutil.isObject(menuJson)) {
                        menuObj = [menuJson];
                    }
                    menuConfig.menus = menuObj;
                } catch(e) {
                    throw new Error(e);
                }
            }

            if(checkutil.isUndefined(menuConfig.menus)) {
                console.warn("datas is not jsonString or JS Object or JS Array, configure failed!!!");
                return;
            }
        }
        if(checkutil.isUndefined(config)) {
            console.log("The function treeMenu.init() doesn't have the parameter 'config' that means the treeMenu has no event!");
        } else {
            if(checkutil.isUndefined(config.treeMenuId)) {
                console.warn("Your TreeMenu config has not key['treeMenuId'], configure failed!!!\nPlease configure your unique treeMenu by treeMenuId!");
                return;
            } else if($(config.treeMenuId).length == 0) {
                console.warn("Cannot find your treeMenu[id: " + config.treeMenuId + "], configure failed!!! ");
                return;
            } else {
                menuConfig.treeMenuId = config.treeMenuId;
            }
            if(checkutil.isUndefined(config.superLevel)) {
                console.warn("Your config has not key['superLevel'], default value is 0 that means you your datalist'superlevel is 0!");
                menuConfig.superLevel = 0;
            } else if(!checkutil.isNumber(config.superLevel)) {
                console.warn("Your config's parameter['superLevel'] shoule be a Number, configure failed!");
                return;
            } else {
                menuConfig.superLevel = config.superLevel;
            }
            if(checkutil.isUndefined(config.multiple)) {
                console.warn("Your config has not key['multiple'], default value is false that means you could open one spMenu at most at the same time!");
                menuConfig.multiple = false;
            } else {
                menuConfig.multiple = config.multiple;
            }

            menuConfig.multiple = config.multiple;
        }
        genDomTree(menuConfig);
        initEvent(menuConfig);
    }

    /**
     * 生成dom树
     * @param {Object} menuConfig
     */
    function genDomTree(menuConfig) {
        var $treeMenu = $(menuConfig.treeMenuId);
        var menus = menuConfig.menus;
        var multiple = menuConfig.multiple;
        var hasTreeMenuClass = $treeMenu.hasClass("TreeMenu");
        if(hasTreeMenuClass === false) {
            $treeMenu.addClass("TreeMenu");
        }
        var firstLevel = menuConfig.superLevel;
        eachMenusDom($treeMenu, menus, firstLevel);

    }

    /**
     * 递归生成dom树
     * @param {Object} superEle 在哪个元素下添加子元素
     * @param {Object} subMenus 子元素的集合 JS Array
     * @param {Object} superLevel 上级元素的等级
     */
    function eachMenusDom(superEle, subMenus, superLevel) {
        var level = superLevel + 1;
        var ulBox01 = $("<ul>", {
            "class": "MenuBox MenuBox-" + level,
        });

        $.each(subMenus, function(i, menu) {

            var liItem = $("<li>", {
                "class": "Level Level-" + level,
            });
            var menuNameDiv = $("<div>", {
                "class": "MenuName",
            });
            var menuLink = $("<a>");

            var hasHref = menu.href && $.trim(menu.href).length > 0;
            if(hasHref === true) {
                menuLink.attr("href", menu.href);
                menuLink.css("width", "100%");
            }
            var hasName = menu.name && menu.name.length > 0;
            if(hasName === true) {
                menuLink.text(menu.name);
            }

            menuLink.appendTo(menuNameDiv);
            menuNameDiv.appendTo(liItem);
            liItem.appendTo(ulBox01);
            ulBox01.appendTo(superEle);
            var subMenus = menu.subMenus;
            if(subMenus && checkutil.isArray(subMenus) && subMenus.length > 0) {
                var arrowDownDiv = $("<div>", {
                    "class": "TreeArrowDown"
                });
                arrowDownDiv.appendTo(menuNameDiv);
                eachMenusDom(liItem, subMenus, level);
            }
            if (menu.active) {
                var current = ulBox01.prev('.MenuName');
                for (var i = level-1; i > 0 ; i--) {
                    initClick(current);
                    current = current.parent('li').parent('.MenuBox').prev('.MenuName')
                }


                liItem.css("background", "#8fd2fb");
            }
        });
    }
    /**
     * 初始化点击事件
     * @param {Object} menuConfig
     */
    function initEvent(menuConfig) {
        $(menuConfig.treeMenuId + " .MenuName").on("click", function() {
            initClick(this);
        });
    }

    function initClick(current) {
        var $arrow = $(current).find(".TreeArrowDown");
        var $menuBox = $(current).next();
        var $menuItem = $(current).parent();
        $menuBox.slideToggle('fast');
        $arrow.toggleClass("rotate");
        $menuItem.toggleClass("active");
        if(menuConfig.multiple === false) {
            var $brothers = $menuItem.siblings();
            $brothers.find(".MenuBox").slideUp('fast');
            $brothers.find(".TreeArrowDown").removeClass("rotate");
            $brothers.removeClass("active");
        }
    }

    window.treeMenu = treeMenu;

}($, JSON, checkutil);