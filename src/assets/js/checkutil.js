/**
 * checkutil工具类
 *
 * @author DarkRanger
 * https://coding.net/u/wrcold520/p/JavaScript-lib/git/blob/master/js/checkutil.js
 */
! function() {
	"use strict";

	var checkutil = {};

	/**
	 * 判断是否尚未定义
	 *
	 * 	示例：声明但未定义
	 * 	var undefinedEle;
	 * 	checkutil.isUndefined(undefinedEle);//true
	 *
	 * @param {Object} obj
	 */
	checkutil.isUndefined = function(obj) {
		return typeof obj === "undefined";
	};
	/**
	 * 判断是否是一个不存在的对象
	 *
	 * 	示例：返回的是一个不存在的对象
	 *	var ele= document.getElementById("donotExistEle");
	 * 	checkutil.isNull(ele);//true
	 *
	 * @param {Object} obj
	 */
	checkutil.isNull = function(obj) {
		return obj === null;
	};

	/**
	 * 当传入一个数参数并调用 Number.isNaN 时，会进行以下几步：
	 *	1. 如果Type(number) 不是数字, 返回 false.
	 *	2. 如果数字是NaN, 返回true.
	 *	3. 其他情况，返回false.
	 *
	 *
	 * 需要注意的是：
	 * 	NaN与其他任何值都不相等，包括它自己
	 * 		NaN===NaN;								//false
	 *
	 * 	isNaN与Number.isNaN是两个不同的方法
	 * 		console.log(isNaN(NaN));				//true
	 *		console.log(isNaN(Number.NaN));			//true
	 *		console.log(Number.isNaN(NaN));			//true
	 *		console.log(Number.isNaN(Number.NaN));	//true
	 * @param {Object} obj
	 */
	checkutil.isNaN = function(obj) {
		return Number.isNaN(obj);
	}

	/**
	 * 判断是否是数组
	 * @param {Object} arr
	 */
	checkutil.isArray = function(arr) {
		return(typeof arr === "object") && arr.constructor === Array;
	};

	/**
	 * 判断是否是boolean值
	 * @param {Object} b
	 */
	checkutil.isBoolean = function(b) {
		return typeof b === "boolean" && b.constructor === Boolean;
	};

	/**
	 * 判断是否是日期
	 */
	checkutil.isDate = function(date) {
		return(typeof date === "object") && date.constructor === Date;
	};

	/**
	 * 判断是否是数字
	 * @param {Object} num
	 */
	checkutil.isNumber = function(num) {
		return(typeof num === "number") && num.constructor === Number;
	};

	/**
	 * 判断是否是字符串
	 * @param {Object} str
	 */
	checkutil.isString = function(str) {
		return(typeof str === "string") && str.constructor == String;
	};

	/**
	 * 判断是否是一个方法/函数
	 * @param {Object} fun
	 */
	checkutil.isFun = function(fun) {
		return(typeof fun === "function") && fun.constructor === Function;
	};

	/**
	 * 判断是否是一个正则表达式
	 * @param {Object} regExp
	 */
	checkutil.isRegExp = function(regExp) {
		return(typeof regExp === "object") && regExp.constructor === RegExp;
	};

	/**
	 * 判断是否是一个Object对象
	 * 注意：
	 * Array、Boolean、Date、Number、String、RegExp、Function、Object是不同的对象
	 * @param {Object} obj
	 */
	checkutil.isObject = function(obj) {
		return(typeof obj === "object") && obj.constructor === Object;
	}

	window.checkutil = checkutil;

	$.fn.autoTextarea = function(options) {
		var defaults={
			maxHeight:null,
			minHeight:$(this).height()
		};
		var opts = $.extend({},defaults,options);
		return $(this).each(function() {
			$(this).bind("paste cut keydown keyup focus blur",function(){
				var height,style=this.style;
				this.style.height = opts.minHeight + 'px';
				if (this.scrollHeight > opts.minHeight) {
					if (opts.maxHeight && this.scrollHeight > opts.maxHeight) {
						height = opts.maxHeight;
						style.overflowY = 'scroll';
					} else {
						height = this.scrollHeight;
						style.overflowY = 'hidden';
					}
					style.height = height + 'px';
				}
			});
		});
	};
}();