/*
 * @Author: xukeshun 
 * @Date: 2015-10-08 14:40:17 
 * @Last Modified by: xukeshun
 * @Last Modified time: 2018-09-19 13:44:11
 */

// Self-executing function to prevent global vars and help with minification
(function (window, undefined) {
	var document = window.document;
	//bug 安卓4.4低版本不支持es6 endwith
	if (typeof String.prototype.endsWith != 'function') {
		String.prototype.endsWith = function (suffix) {
			return this.indexOf(suffix, this.length - suffix.length) !== -1;
		};
	}
	// Singleton, to prevent multiple initialization
	if (window.cc_js_Player) {
		window.cc_js_Player.showPlayer();
		return;
	}

	// player
	function Player() {
	};

	// Use prototype to init function
	Player.prototype = {
		adId: '',
		adTime: '',
		ans: {},
		answerd: {},
		adPlay: true,
		adPlayEnded: true,
		canskip: '',
		isAdTime: false,
		isQuestionsShow: false,
		materialId: '',
		paramsObj: {},
		picSrc: '',
		startPlayed: false,
		videoAd: '',
		videoInfo: new Array(),
		videoSrc: '',
		//线路切换 获取当前copies的idx
		currentCopiesIdx: null,
		isQQ: function () {
			return navigator.userAgent.indexOf('MQQBrowser') > -1;
		},
		isWeixin: function () {
			return navigator.userAgent.indexOf('MicroMessenger') > -1;
		},
		isIE: function () {
			return !!window.ActiveXObject || "ActiveXObject" in window;
		},
		isIPad: function () {
			return navigator.userAgent.match(/iPad/i) != null;
		},
		isIPhone: function () {
			return navigator.userAgent.match(/iPhone/i) != null;
		},
		isAndroid: function () {
			return navigator.userAgent.match(/Android/i) != null;
		},
		isAndroid2: function () {
			return navigator.userAgent.match(/Android 2/i) != null;
		},
		isSymbianOS: function () {
			return navigator.userAgent.match(/SymbianOS/i) != null;
		},
		isWindowsPhoneOS: function () {
			return navigator.userAgent.match(/Windows Phone/i) != null;
		},
		showPlayer: function () {
			try {
				var head = document.getElementsByTagName("head")[0] || document.documentElement;
				var ucVideoRecording = document.createElement("style");
				ucVideoRecording.classList.add('ccH5videoStyleTag')
				ucVideoRecording.innerHTML = "body .uc-video-toolbar{display:none !important;}";
				head.appendChild(ucVideoRecording);
			} catch (e) {

			}

			var scripts = document.getElementsByTagName("script");
			for (var i = 0; i < scripts.length; i = i + 1) {
				var script = scripts[i];
				//测试环境 修改地址 https://p.bokecc.com/player   to   p.js
				if (script.src.indexOf("http://union.bokecc.com/player") == -1 && script.src.indexOf("http://p.bokecc.com/player") == -1
					&& script.src.indexOf("https://union.bokecc.com/player") == -1 && script.src.indexOf("https://p.bokecc.com/player") == -1
				) {
					continue;
				}
				var src = script.src;
				script.src = "";
				var params = this.getParam(src.split("?")[1]);
				var video = document.createElement("div");
				var randomid = Math.ceil(Math.random() * 10000000);
				video.id = "cc_video_" + params.vid + "_" + randomid;
				params.divid = video.id;
				video.style.width = params.width;
				video.style.height = params.height;
				if (this.isMoble()) {
					video.style.position = "relative";
				}
				video.innerHTML = "";
				// save video params
				this.videoInfo.push(params);
				script.parentNode.replaceChild(video, script);
				var t = new Date().getTime() + '_' + randomid;
				cc_js_Player.paramsObj[t] = params;
				this.jsonp("https://imedia.bokecc.com/servlet/mobile/adloader?uid=" + params.siteid + "&vid=" + params.vid + "&type=1&t=" + t, "cc_js_Player.videoLoad", function () {
					param = params;
					var authCode = '';
					if (typeof window.get_cc_verification_code != 'undefined') {
						authCode = encodeURIComponent(get_cc_verification_code(param.vid));
					}
					if (cc_js_Player.isAndroid() && !cc_js_Player.isAndroid2()) {
						cc_js_Player.jsonp("https://p.bokecc.com/servlet/getvideofile?vid="
							+ param.vid + "&siteid=" + param.siteid + "&divid="
							+ param.divid + "&width=" + encodeURIComponent(window.screen.width, "UTF-8") + "&useragent="
							+ param.userAgent + "&version=20140214" + "&hlssupport=1&vc=" + authCode + "&mediatype=" + param.mediatype, "cc_js_Player.showPlayerView");
					} else if (cc_js_Player.isIPhone() || cc_js_Player.isIPad()) {
						cc_js_Player.jsonp("https://p.bokecc.com/servlet/getvideofile?vid="
							+ param.vid + "&siteid=" + param.siteid + "&divid="
							+ param.divid + "&width=" + encodeURIComponent(param.width, "UTF-8") + "&useragent="
							+ param.userAgent + "&version=20140214" + "&hlssupport=1&vc=" + authCode + "&mediatype=" + param.mediatype, "cc_js_Player.showPlayerView");
					} else {
						cc_js_Player.jsonp("https://p.bokecc.com/servlet/getvideofile?vid="
							+ param.vid + "&siteid=" + param.siteid + "&divid="
							+ param.divid + "&width=" + encodeURIComponent(param.width, "UTF-8") + "&useragent="
							+ param.userAgent + "&version=20140214" + "&hlssupport=1&vc=" + authCode + "&mediatype=" + param.mediatype, "cc_js_Player.showPlayerView");
					}
				});
			}
		},
		showPlayerDirect: function (initParams) {
			var params = initParams;
			params.userAgent = this.getUserAgent();
			var randomid = Math.ceil(Math.random() * 10000000);
			this.addVideoElement(document.body, params, randomid);
			this.playAd(params, randomid);
		},
		playAd: function (params, randomid) {
			var t = new Date().getTime() + '_' + randomid;
			cc_js_Player.paramsObj[t] = params;
			this.jsonp("https://imedia.bokecc.com/servlet/mobile/adloader?uid=" + params.siteid + "&vid=" + params.vid + "&type=1&t=" + t, "cc_js_Player.videoLoad", function () {
				param = params;
				var authCode = '';
				if (typeof window.get_cc_verification_code != 'undefined') {
					authCode = encodeURIComponent(get_cc_verification_code(param.vid));
				}
				if (cc_js_Player.isAndroid() && !cc_js_Player.isAndroid2()) {
					cc_js_Player.jsonp("https://p.bokecc.com/servlet/getvideofile?vid="
						+ param.vid + "&siteid=" + param.siteid + "&divid="
						+ param.divid + "&width=" + encodeURIComponent(window.screen.width, "UTF-8") + "&useragent="
						+ param.userAgent + "&version=20140214" + "&hlssupport=1&vc=" + authCode + "&mediatype=" + param.mediatype, "cc_js_Player.showPlayerView");
				} else if (cc_js_Player.isIPhone() || cc_js_Player.isIPad()) {
					cc_js_Player.jsonp("https://p.bokecc.com/servlet/getvideofile?vid="
						+ param.vid + "&siteid=" + param.siteid + "&divid="
						+ param.divid + "&width=" + encodeURIComponent(param.width, "UTF-8") + "&useragent="
						+ param.userAgent + "&version=20140214" + "&hlssupport=1&vc=" + authCode + "&mediatype=" + param.mediatype, "cc_js_Player.showPlayerView");
				} else {

					cc_js_Player.jsonp("https://p.bokecc.com/servlet/getvideofile?vid="
						+ param.vid + "&siteid=" + param.siteid + "&divid="
						+ param.divid + "&width=" + encodeURIComponent(param.width, "UTF-8") + "&useragent="
						+ param.userAgent + "&version=20140214" + "&hlssupport=1&vc=" + authCode + "&mediatype=" + param.mediatype, "cc_js_Player.showPlayerView");
				}
			});
		},
		addVideoElement: function (parent, params, randomid) {
			var video = document.createElement("div");
			video.id = "cc_video_" + params.vid + "_" + randomid;
			params.divid = video.id;
			video.style.width = params.width;
			video.style.height = params.height;
			if (this.isMoble()) {
				video.style.position = "relative";
			}
			video.innerHTML = "";
			// save video params
			this.videoInfo.push(params);
			parent.appendChild(video);
		},
		videoLoad: function (data) {
			var ts = data.response.t;
			param = cc_js_Player.paramsObj[ts];
			var authCode = '';
			if (typeof window.get_cc_verification_code != 'undefined') {
				authCode = encodeURIComponent(get_cc_verification_code(param.vid));
			}
			if (this.isAndroid() && !this.isAndroid2()) {
				this.jsonp("https://p.bokecc.com/servlet/getvideofile?vid="
					+ param.vid + "&siteid=" + param.siteid + "&divid="
					+ param.divid + "&width=" + encodeURIComponent(window.screen.width, "UTF-8") + "&useragent="
					+ param.userAgent + "&version=20140214" + "&hlssupport=1&vc=" + authCode + "&mediatype=" + param.mediatype, "cc_js_Player.showPlayerView");
			} else if (this.isIPhone() || this.isIPad()) {
				this.jsonp("https://p.bokecc.com/servlet/getvideofile?vid="
					+ param.vid + "&siteid=" + param.siteid + "&divid="
					+ param.divid + "&width=" + encodeURIComponent(param.width, "UTF-8") + "&useragent="
					+ param.userAgent + "&version=20140214" + "&hlssupport=1&vc=" + authCode + "&mediatype=" + param.mediatype, "cc_js_Player.showPlayerView");
			} else {

				this.jsonp("https://p.bokecc.com/servlet/getvideofile?vid="
					+ param.vid + "&siteid=" + param.siteid + "&divid="
					+ param.divid + "&width=" + encodeURIComponent(param.width, "UTF-8") + "&useragent="
					+ param.userAgent + "&version=20140214" + "&hlssupport=1&vc=" + authCode + "&mediatype=" + param.mediatype, "cc_js_Player.showPlayerView");
			}

			if (typeof window.is_skip_ad === 'function') {
				var ad = encodeURIComponent(is_skip_ad());
				if (ad != 1) {
					cc_js_Player.adLoader(data);
				}
			} else {
				cc_js_Player.adLoader(data);
			}
		},
		adLoader: function (j) {
			if (j.response.result == 1) {
				var str = j.response.ad[0].material;
				var pos = str.lastIndexOf(".");
				var lastname = str.substring(pos, str.length);
				if (lastname.toLowerCase() == '.mp4') {
					if (j.response.ad[0].material.indexOf("bokecc.com") != -1) {
						this.videoSrc = j.response.ad[0].material.replace(/http:/i, "https:");
					} else {
						this.videoSrc = j.response.ad[0].material;
					}
				} else {
					if (j.response.ad[0].material.indexOf("bokecc.com") != -1) {
						this.picSrc = j.response.ad[0].material.replace(/http:/i, "https:");
					} else {
						this.picSrc = j.response.ad[0].material;
					}
				}
				this.adUrl = j.response.ad[0].material;
				this.canClick = j.response.canclick;
				this.videoAd = j.response.result;
				this.adTime = j.response.time;
				this.skipTime = j.response.skiptime;
				this.canskip = j.response.canskip;
				this.materialUrl = j.response.ad;
				this.adId = j.response.adid;
				this.materialId = j.response.ad[0].materialid;
				this.clickurl = j.response.ad[0].clickurl;
				if (!!j.response.statisUrl) {
					this.imgLoad(j.response.statisUrl);
				}
				this.isAdTime = true;
			}
		},
		getAdSrc: function () {
			return "https://imedia.bokecc.com/servlet/mobile/clickstats?adid="
				+ this.adId + "&clickurl=" + encodeURIComponent(this.clickurl) + "&materialid=" + this.materialId;
		},
		getParam: function (queryString) {
			var params = queryString.split("&");
			var result = { mediatype: 1 };
			for (var i = 0; i < params.length; i = i + 1) {
				var key_value = params[i].split("=");
				var key = (key_value[0] + "").replace(/(^\s*)|(\s*$)/g, "");// trim
				result[key] = key_value[1];
			}
			result.userAgent = this.getUserAgent();
			return result;
		},
		getUserAgent: function () {
			var userAgent = navigator.userAgent.match(/MSIE|Firefox|iPad|iPhone|Android|SymbianOS/);
			var winPhoneUA = navigator.userAgent.match(/Windows Phone/);
			if (winPhoneUA == "Windows Phone") {
				userAgent = winPhoneUA;
			}

			if (userAgent) {
				return userAgent;
			} else {
				return "other";
			}
		},
		isMoble: function () {
			var userAgent = navigator.userAgent.match(/iPhone|Android|SymbianOS|Windows Phone/);
			if (userAgent) {
				return true;
			} else {
				return false;
			}
		},
		getVideoCode: function (params, video) {
			var videoCode = "";
			var _this = this;
			function getFileExt(num1, num2) {
				var filename = _this.getDefaultCopy(video).playurl;
				var index1 = filename.lastIndexOf(".");
				var index2 = filename.length;
				var postf = filename.substring(index1, index2);
				return postf.substring(num1, num2)
			};
			function checkPcm() {
				return video.status == 1 && getFileExt(0, 4) == '.pcm';
			};
			function checkBrowser() {
				var m3u8 = navigator.userAgent.indexOf('Firefox') >= 0 && getFileExt(0, 5) == '.m3u8' && _this.isAndroid();
				var qq = (_this.isAndroid() || _this.isIPad()) && _this.isQQ() && !_this.isWeixin() && video.disableQQ == 1;
				return qq || m3u8;
			}
			function checkFlash() {
				var hasFlash = true;
				if (_this.isIE()) {
					try {
						var objFlash = new ActiveXObject("ShockwaveFlash.ShockwaveFlash");
					} catch (e) {
						hasFlash = false;
					}
				} else {
					if (!navigator.plugins["Shockwave Flash"]) {
						hasFlash = false;
					}
				}
				return hasFlash;
			};
			function isCanPlayDomain() {
				if (typeof video.H5domain === 'undefined') {
					return true;
				}
				var oUrl = window.location.host;
				var blackURL = video.H5domain.blacklist;
				var whiteURL = video.H5domain.whitelist;

				if (!!blackURL && blackURL.length > 0) {
					for (key in blackURL) {
						if (String(oUrl).length > blackURL[key].length) {
							if (String(oUrl).endsWith('.' + blackURL[key].toLowerCase())) {
								return false;
							}
						} else if (String(oUrl).length == blackURL[key].length) {
							if (String(oUrl) == blackURL[key].toLowerCase()) {
								return false;
							}
						}
					}
					return true;
				} else if (!!whiteURL && whiteURL.length > 0) {
					for (key in whiteURL) {
						if (String(oUrl).length > whiteURL[key].length) {
							if (String(oUrl).endsWith('.' + whiteURL[key].toLowerCase())) {
								return true;
							}
						} else if (String(oUrl).length == whiteURL[key].length) {
							if (String(oUrl) == whiteURL[key].toLowerCase()) {
								return true;
							}
						}
					}
					return false;
				}
				return true;
			};
			// noraml player
			if (this.isSymbianOS()) {
				videoCode = this.createMobileView(params, video);
			} else if (video.playtype == 1 && this.isIE()) {
				if (!checkFlash()) {
					videoCode = this.createFlashDiv(params);
				} else {
					videoCode = this.createFlashView(params);
				}
			} else if (this.isAndroid() || video.playtype == 1 || this.isIPad() || this.isWindowsPhoneOS() || this.isIPhone()) {
				if (!isCanPlayDomain()) {
					videoCode = this.createTips(params, '当前域名未获得播放授权');
				} else if (checkPcm()) {
					videoCode = this.createTips(params, '此视频已加密，请在其他设备上观看');
				} else if (checkBrowser()) {
					videoCode = this.createTips(params, '请切换其他浏览器观看视频');
				} else {
					videoCode = this.createHTML5VideoView(params, video);
				}
			} else {
				if (!checkFlash()) {
					videoCode = this.createFlashDiv(params);
				} else {
					videoCode = this.createFlashView(params);
				}
			}
			return videoCode;
		},
		getDefaultCopy: function (video) {
			//修复单独播放音频时会把音频都过滤的问题
			if (video.copies[0].mediatype !== 2) {
				video.copies = video.copies.filter(function (v) { return v.mediatype === 1 });
			}
			for (var c in video.copies) {
				if (video.defaultquality == video.copies[c].quality) {
					//线路切换 记录当前copies的idx
					this.currentCopiesIdx = c;
					return video.copies[c];
				}
			}
			return video.copies[0];
		},
		isRealtimePlay: function (video) {
			return video.originalplay == 1 && video.status == 2;
		},
		getAudioSrc: function (video) {
			//++ 修改成根据mideatype 判断是否是音频
			// var length = video.copies.length;
			// return video.copies[length - 1].playurl;
			var audios = [];
			video.copies.forEach(function (v) {
				if (v.mediatype === 2) {
					audios.push(v);
				}
			});
			if (audios.length === 0) {
				return '';
			}
			return audios[0].playurl.replace(/http:/i, "https:");
		},
		getSrc: function (params, video) {
			var src;
			var ua = window.navigator.userAgent.toLowerCase();
			if (this.videoAd == 1 && this.videoSrc.indexOf(".mp4") != -1) {
				if (ua.match(/MicroMessenger/i) == 'micromessenger' && this.isAndroid() && video.uid == '238125') {
					src = this.getDefaultCopy(video).playurl.replace(/http:/i, "https:");
				} else {
					src = this.videoSrc;
				}
			} else {
				if (this.isRealtimePlay(video)) {
					//原片播放修改
					// src = 'https://express.play.bokecc.com/' + params.siteid + '/' + params.vid + '.mp4';
					src = video.copies[0].playurl.replace(/http:/i, "https:");
				} else if (typeof window.get_custom_id === 'function') {
					var flow = encodeURIComponent(get_custom_id());
					src = this.getDefaultCopy(video).playurl.replace(/http:/i, "https:") + '&custom_id=' + flow;
				} else {
					src = this.getDefaultCopy(video).playurl.replace(/http:/i, "https:");
				}
			}
			//电视盒子bug 
			if ((/(iPad; CPU OS 5_0_1 like Mac OS X)/i).test(navigator.userAgent) || (/Chrome\/30\.0\.0\.0 Safari\/537\.36/i).test(navigator.userAgent)) {
				src = src.replace(/https:/i, "http:")
			}
			return src;
		},
		skipSrc: function (params, video) {
			var src;
			if (this.isRealtimePlay(video)) {
				//原片修改
				// src = 'https://express.play.bokecc.com/' + params.siteid + '/' + params.vid + '.mp4';
				src = video.copies[0].playurl.replace(/http:/i, "https:");
			} else if (typeof window.get_custom_id === 'function') {
				var flow = encodeURIComponent(get_custom_id());
				src = this.getDefaultCopy(video).playurl.replace(/http:/i, "https:") + '&custom_id=' + flow;
			} else {
				src = this.getDefaultCopy(video).playurl.replace(/http:/i, "https:");
			}
			return src;
		},
		getVttTrack: function (params, video) {
			var vttSrc;
			var vttTrack;
			if (typeof video.vtt != 'undefined' && video.vtt) {
				if (video.vtt.vttUrl.indexOf("bokecc.com") != -1) {
					vttSrc = video.vtt.vttUrl.replace(/http:/i, "https:");
				} else {
					vttSrc = video.vtt.vttUrl;
				}
				vttTrack = "<track src='" + vttSrc + "' srclang='zh-cn' label='简体中文' kind='subtitles' default>";
			} else {
				vttTrack = '';
			}
			return vttTrack;
		},
		getCross: function (params, video) {
			var cross;
			if ((typeof video.vtt != 'undefined' && video.vtt) || video.vrmode == 1) {
				cross = 'crossorigin';
			} else {
				cross = '';
			}
			return cross;
		},
		getImgSrc: function (params, video) {
			var imgSrc;
			if (!!params.img_path) {
				imgSrc = params.img_path;
			} else if (this.isRealtimePlay(video)) {
				//原片修改
				// imgSrc = 'https://express.play.bokecc.com/' + params.siteid + '/' + params.vid + '.jpg';
				imgSrc = video.img.replace(/http:/i, "https:");
			} else {
				imgSrc = video.img.replace(/http:/i, "https:");
			}
			return imgSrc;
		},
		getCopy: function (params, video) {
			var str;
			if (this.isRealtimePlay(video)) {
				str = '';
			} else {
				str = this.getDefaultCopy(video).desp;
			}
			return str;
		},
		createMobileView: function (params, video) {
			var twidth = params.width;
			var theight = params.height;
			if (!isNaN(params.width) || !isNaN(params.height)) {
				twidth = params.width + 'px';
				theight = params.height + 'px';
			}
			if (video.status == 1) {
				// var buttonwidth = 70;
				// var buttonheight = 50;
				// var top = (params.height - buttonheight) / 2;
				// var left = (params.width - buttonwidth) / 2;
				return "<table width='" + params.width + "' height='" + params.height + "' style='position:relative;'><tr><td width='" + params.width + "' height='" + params.height + "' style='position:relative;'><img src='"
					+ video.img + "' width='" + params.width + "' height='" + params.height + "' style='width:" + twidth + "; height:" + theight + ";' />"
					+ "<a href='" + this.getDefaultCopy(video).playurl
					+ "'><img src='https://p.bokecc.com/images/01.png' style='position:absolute; top:50%; left:50%; margin:-25px 0 0 -35px; width:70px; height:50px;' /></a></td></tr></table>";
			} else if (video.status == 0) {
				return "<table><tr><td align='center' width='" + params.width + "' height='"
					+ params.height + "' style='background:#000; color:#FFF; font-size:30px; -webkit-text-size-adjust:none;'>抱歉，视频服务出现异常，请联系网站管理员。</td></tr></table>";
			} else if (video.status == 2) {
				return "<table><tr><td align='center' width='" + params.width + "' height='"
					+ params.height + "' style='background:#000; color:#FFF; font-size:30px; -webkit-text-size-adjust:none;'>视频处理中……</td></tr></table>";
			} else if (video.status == 4) {
				return "<table><tr><td align='center' width='" + params.width + "' height='"
					+ params.height + "' style='background:#000; color:#FFF; font-size:30px; -webkit-text-size-adjust:none;'>抱歉，网络连接失败，请刷新重试或联系网站管理员。</td></tr></table>";
			} else {
				return "<table><tr><td align='center' width='" + params.width + "' height='"
					+ params.height + "' style='background:#000; color:#FFF; font-size:30px; -webkit-text-size-adjust:none;'>抱歉，暂不支持本设备，请选择其他设备观看。</td></tr></table>";
			}
		},
		createHTML5VideoView: function (params, video) {
			var twidth = params.width;
			var theight = params.height;
			if (!isNaN(params.width) || !isNaN(params.height)) {
				twidth = params.width + 'px';
				theight = params.height + 'px';
			}
			if (video.status == 1) {
				return this.creatH5player(params, video);
			} else if (video.status == 0) {
				return "<table style='border:none; padding:0; margin:0; width:" + twidth + "; height:" + theight + ";'><tr><td align='center' width='" + params.width + "' height='"
					+ params.height + "' style='background:#000; border:none; padding:0; margin:0; color:#FFF; font-size:30px; -webkit-text-size-adjust:none;'>抱歉，视频服务出现异常，请联系网站管理员。</td></tr></table>";
			} else if (video.status == 2) {
				if (this.isRealtimePlay(video)) {
					return this.creatH5player(params, video);
				} else {
					return "<table style='border:none; padding:0; margin:0; width:" + twidth + "; height:" + theight + ";'><tr><td align='center' width='" + params.width + "' height='"
						+ params.height + "' style='background:#000; border:none; padding:0; margin:0; color:#FFF; font-size:30px; -webkit-text-size-adjust:none;'>视频处理中……</td></tr></table>";
				}
			} else if (video.status == 4) {
				return "<table style='border:none; padding:0; margin:0; width:" + twidth + "; height:" + theight + ";'><tr><td align='center' width='" + params.width + "' height='"
					+ params.height + "' style='background:#000; border:none; padding:0; margin:0; color:#FFF; font-size:30px; -webkit-text-size-adjust:none;'>抱歉，网络连接失败，请刷新重试或联系网站管理员。</td></tr></table>";
			} else {
				return "<table style='border:none; padding:0; margin:0; width:" + twidth + "; height:" + theight + ";'><tr><td align='center' width='" + params.width + "' height='"
					+ params.height + "' style='background:#000; border:none; padding:0; margin:0; color:#FFF; font-size:30px; -webkit-text-size-adjust:none;'>抱歉，暂不支持本设备，请选择其他设备观看。</td></tr></table>";
			}
		},
		creatH5player: function (params, video) {
			if (video.authenable == 0 && !!video.freetime && video.copies[0].playurl.indexOf('.m3u8') !== -1) {
				//遍历copies,修改播放地址
				video.copies.forEach(function (v) {
					v.playurl = v.playurl + "&freetime=" + video.freetime;
					v.backupurl = v.backupurl + "&freetime=" + video.freetime;
				})
			}
			//2019-1-16 自动播放改版
			var autoplay = '';
			if (params.autoStart === "true") {
				autoplay = 'autoplay=autoplay'
			}
			//代码结束
			return "<div class='ccH5playerBox'>"
				+ "  <div  id='ccH5historyTimeBox'>"
				+ "		<span id='ccH5jumpInto' >上次观看到<i>00:50</i><b>点击继续</b></span>"
				+ "		<span id='ccH5delBox' >&times;</span>"
				+ "   </div>"
				+ "  <div class='switchVttBox' id='switchVttBox'>"
				+ "  	<div class='container'>"
				+ "	 	 	<ul><li></li><li></li><li></li><li></li></ul>"
				+ "	 	</div>"
				+ "	 </div>"
				+ "  <div class='picAd' style='display:none;'>"
				+ "      <a class='picBtn' href='' ></a>"
				+ "  	<div class='x-advert-info'>"
				+ "	  		<div class='x-advert-skip'>"
				+ "		 		<div class='x-advert-txt closePicAd'><span class='closePicTime'><i class='skipPicNum'></i>s后可</span>关闭广告</div>"
				+ "		 		<div class='x-mask'></div>"
				+ "	  		</div>"
				+ "	  		<div class='x-advert-countdown'>"
				+ "		 		<div class='x-advert-txt'><span class='adSec'></span>s</div>"
				+ "		 		<div class='x-mask'></div>"
				+ "	  		</div>"
				+ "  	</div>"
				+ "		<div class='pictab'><img onload='oPlayer.picAdStyle()' class='pSrc' src=''/></div>"
				+ "  	<div class='x-advert-detail'>"
				+ "	  		<div class='x-advert-txt'><a class='picTxth' href='' target='_blank'>了解详情</a></div>"
				+ "	  		<div class='x-mask'></div>"
				+ "  	</div>"
				+ "	</div>"
				+ "  <div class='x-advert' style='display:none;'>"
				+ "		<a class='skipGg' href='' ></a>"
				+ "  	<div class='x-advert-info'>"
				+ "	  		<div class='x-advert-skip'>"
				+ "		 		<div class='x-advert-txt closeAd'><span class='closeTime'><i class='skipNum'></i>s后可</span>关闭广告</div>"
				+ "		 		<div class='x-mask'></div>"
				+ "	  		</div>"
				+ "	  		<div class='x-advert-countdown'>"
				+ "		 		<div class='x-advert-txt'><span class='x-advert-sec'></span>s</div>"
				+ "		 		<div class='x-mask'></div>"
				+ "	  		</div>"
				+ "  	</div>"
				+ "  	<div class='x-advert-detail'>"
				+ "	  		<div class='x-advert-txt'><a class='vadSrc' href=''  target='_blank'>了解详情</a></div>"
				+ "	  		<div class='x-mask'></div>"
				+ "  	</div>"
				+ "  </div>"
				+ "	<div class='ccH5Info'></div>"
				+ "	<div class='ccH5Loading'></div>"
				+ "	<div class='ccH5Poster'><img src='" + this.getImgSrc(params, video) + "' /></div>"
				// + "	<video id='cc_" + params.vid + "' x-webkit-airplay='allow' " + x5player + " x5-playsinline webkit-playsinline playsinline='true' " + this.getCross(params, video) + " width='" + params.width + "' height='" + params.height + "' src='" + this.getSrc(params, video) + "'>" + this.getVttTrack(params, video) + "</video>"
				+ "	<video  id='cc_" + params.vid + "' x-webkit-airplay='allow'  x5-playsinline webkit-playsinline playsinline='true' " + this.getCross(params, video) + " width='" + params.width + "' " + autoplay + " >"
				+ this.getVttTrack(params, video)
				//bug 修复安卓4.4.2等老机型无法播放的bug
				//	+ "<source  src='" + this.getSrc(params, video) + "' type='video/mp4' >"
				+ "<source  src='" + this.getSrc(params, video) + "' >"
				+ "</video>"
				//audio
				+ " <audio>"
				+ "<source  src='" + this.getAudioSrc(video) + "' >"
				+ "</audio>"
				+ "<div  class='ccH5More ccH5FadeOut'>"
				+ "<ul id='ccBtnList' class='ccBtnList'>"
				+ "	    <li id='ccH5SwitchUrl' class='ccH5SwitchUrl'>切换线路</li>"
				+ "		<li id='ccH5SwitchAudio' class='ccH5SwitchAudio'>音频播放</li>"
				+ "		<li id='ccH5switchVtt' class='ccH5SwitchAudio'>字幕切换</li>"
				+ "</ul>"
				+ " </div>"
				+ "	<div id='replaybtn' class='ccH5PlayBtn'></div>"
				+ "	<div class='ccH5PlayBtn2'></div>"
				+ "	<div class='ccH5AudioBg'>"
				+ "<img src='https://p.bokecc.com/images/html5player/ccH5AudioBg.png' >"
				+ "<p class='ccbgText'>音频播放中</p>"
				+ "<div class='ccbgButton'>切回视频</div>"
				+ "</div>"
				+ "	<section class='ccH5FadeOut'>"
				+ "		<div class='ccH5ProgressBar'>"
				+ "			<div class='cch5CurrentTime' id='cch5CurrentTime'>"
				+ "					<p></p>"
				+ "					<div class='trangel'></div>"
				+ "			</div>"
				+ "			<div class='ccH5LoadBar'>"
				+ "				<div class='ccH5CurrentPro'>"
				+ "					<span class='ccH5DragBtn'></span>"
				+ "				</div>"
				+ "			</div>"
				+ "		</div>"
				+ "		<span class='ccH5TogglePlay'></span>"
				+ "		<div class='ccH5Time'>"
				+ "			<em class='ccH5TimeCurrent'>00:00</em><span>/</span><em class='ccH5TimeTotal'>00:00</em>"
				+ "		</div>"
				+ "		<span class='ccH5vm'>T</span>"
				+ "		<div class='ccH5vmdiv'>"
				+ "			<div class='ccH5vmbar'>"
				+ "				<div class='ccH5vmbarPro'>"
				+ "					<span class='ccH5DragVmBtn'></span>"
				+ "				</div>"
				+ "			</div>"
				+ "		</div>"
				+ "		<span class='ccH5sp'>" + decodeURIComponent('%E5%B8%B8%E9%80%9F') + "</span>"
				+ "		<ul class='ccH5spul'>"
				+ "			<li>2" + decodeURIComponent('%E5%80%8D') + "</li>"
				+ "			<li>1.5" + decodeURIComponent('%E5%80%8D') + "</li>"
				+ "			<li>1.2" + decodeURIComponent('%E5%80%8D') + "</li>"
				+ "			<li class='selected'>" + decodeURIComponent('%E5%B8%B8%E9%80%9F') + "</li>"
				+ "			<li>0.8" + decodeURIComponent('%E5%80%8D') + "</li>"
				+ "		</ul>"
				+ "		<span class='ccH5hd'>" + this.getCopy(params, video) + "</span>"
				+ "		<ul class='ccH5hdul'>"
				+ "		</ul>"
				+ "		<em class='ccH5FullsBtn'>B</em>"
				+ "		<em class='ccH5ExitFullsBtn'>B</em>"
				+ "	</section>"
				+ "</div>";
		},
		createFlashDiv: function (params) {
			return "<div style='width: 100%; background: #000; color: #fff; text-align: center; font-size: 18px;'>"
				+ "   <div style='line-height:" + params.height + "px;'>"
				+ "   <span style='font-size:18px'>" + decodeURIComponent('%E6%82%A8%E8%BF%98%E6%B2%A1%E6%9C%89%E5%AE%89%E8%A3%85flash%E6%92%AD%E6%94%BE%E5%99%A8%EF%BC%8C%E8%AF%B7%E7%82%B9%E5%87%BB') + "<a style='color:#06a7e1' href='http://www.adobe.com/go/getflash' target='_blank'>" + decodeURIComponent('%E8%BF%99%E9%87%8C') + "</a>" + decodeURIComponent('%E5%AE%89%E8%A3%85') + "</span>"
				+ "   </div>"
				+ "</div>";
		},
		createTips: function (params, text) {
			var theight = params.height;
			if (!isNaN(params.height)) {
				theight = params.height + 'px';
			}
			return "<div style='width: 100%; height:" + theight + "; background: #000;color: #fff; text-align: center; font-size: 18px; display:table;'>"
				+ "   <div style='display:table-cell; vertical-align: middle;'>"
				+ "   <span id='pcmTips' style='font-size:18px'>" + text + "</span>"
				+ "   </div>"
				+ "</div>";
		},
		createFlashView: function (params) {
			var flash_bg_path = "";
			var flash_img_path = "";
			if (!!params.img_path) {
				flash_bg_path = "<param name='flashvars' value='img_path=" + params.img_path + "' />";
				flash_img_path = " flashvars='img_path=" + params.img_path + "'";
			}
			return "<object classid='clsid:D27CDB6E-AE6D-11cf-96B8-444553540000' "
				+ "codebase='http://download.macromedia.com/pub/shockwave/cabs/flash/swflash."
				+ "cab#version=7,0,0,0' width='" + params.width + "' height='" + params.height
				+ "' id='cc_" + params.vid + "'>"
				+ "<param name='movie' value='https://p.bokecc.com/flash/player.swf?vid="
				+ params.vid + "&siteid=" + params.siteid + "&playerid=" + params.playerid
				+ "&playertype=" + params.playertype + "&autoStart=" + params.autoStart + "&mediatype=" + param.mediatype + "' />"
				+ "<param value='transparent' name='wmode' />"
				+ "<param name='allowFullScreen' value='true' />"
				+ "<param name='allowScriptAccess' value='always' />"
				+ flash_bg_path
				+ "<embed src='https://p.bokecc.com/flash/player.swf?vid="
				+ params.vid + "&siteid=" + params.siteid + "&playerid=" + params.playerid
				+ "&playertype=" + params.playertype
				+ "&autoStart=" + params.autoStart + "&mediatype=" + param.mediatype + "' width='" + params.width + "' height='" + params.height
				+ "' name='cc_" + params.vid + "' wmode='transparent' allowFullScreen='true' allowScriptAccess='always'"
				+ " pluginspage='http://www.macromedia.com/go/getflashplayer' " + flash_img_path
				+ "type='application/x-shockwave-flash'/></object>";
		},
		createPassword: function (params, video) {
			var theight = params.height;
			var twidth = params.width;
			if (!isNaN(params.height)) {
				theight = params.height + 'px';
			}
			if (!isNaN(params.width)) {
				twidth = params.width + 'px';
			}
			return "<div style='width:" + twidth + "; height:" + theight + "; min-height:280px; background: #000;color: #fff; text-align: center; font-size: 14px; display:table;'>"
				+ "	<div id='pwdBox' style='display:table-cell; vertical-align: middle; font-size:20px;'>"
				+ "		<div id='pwdTips' style='display:none;font-size:14px;'></div>"
				+ "		<input id='ccPwd' style='border-radius:4px;-webkit-appearance: none; padding:6px;font-size:15px;border:0;height:20px;' type='password' placeholder='请输入密码'>"
				+ "		<input id='ccSubmit' style='border-radius:4px;-webkit-appearance: none; padding:7px 12px;border:0;background:#e1742d;color:#fff;height:32px;' type='submit' value='确认'>"
				+ "	</div>"
				+ "</div>"
		},
		checkPassword: function (video, params) {
			var pwdTips = document.getElementById('pwdTips');
			var ccSub = document.getElementById("ccSubmit");
			var pwdBox = document.getElementById("pwdBox");
			var _this = this;
			var num = 2;
			ccSub.onclick = function () {
				var pwd = document.getElementById('ccPwd').value;
				if (hex_md5(pwd) == video.passwd) {
					var video_div = document.getElementById(video.divid);
					video_div.innerHTML = _this.getVideoCode(params, video);
					_this.playerView(video, params);
				} else {
					pwdTips.style.display = 'block';
					if (num == 0) {
						pwdBox.innerHTML = "<span>您已多次尝试失败，请联系管理员</span>";
					} else {
						pwdTips.innerHTML = "<span style='color:#ff2944;'>密码错误，请重新输入</span>（剩余" + num + "次机会）";
						num--;
					}
				}
			}
		},
		jsonp: function (url, callback, errorCallback) {
			// use setTimeout to handle multiple requests problem, force them in
			// a queue
			var t1 = new Date().getTime();
			setTimeout(function () {
				var head = document.getElementsByTagName("head")[0] || document.documentElement;
				var script = document.createElement("script");
				// add the param callback to url, and avoid cache
				script.src = url + "&callback=" + callback + "&r=" + Math.random() * 10000000;
				// Use insertBefore instead of appendChild to circumvent an IE6
				// bug.
				// This arises when a base node is used.
				head.insertBefore(script, head.firstChild);
				var hasCallbacked = false;
				script.onload = script.onreadystatechange = function () {
					// use /loaded|complete/.test( script.readyState ) to test
					// IE6 ready,!this.readyState to test FF
					if (!this.readyState || /loaded|complete/.test(script.readyState)) {
						hasCallbacked = true;
						// Handle memory leak in IE
						script.onload = script.onreadystatechange = null;
						if (head && script.parentNode) {
							head.removeChild(script);
						}
					}
					var t2 = new Date().getTime();
					window.playApiTime = Math.floor(t2 - t1);
				};
				script.onerror = function () {
					hasCallbacked = true;
					if (errorCallback) {
						errorCallback();
					}
				};
				setTimeout(function () {
					if (!hasCallbacked) {
						if (errorCallback) {
							errorCallback();
						}
					}
					if (head && script.parentNode) {
						head.removeChild(script);
					}
				}, 5000);
			}, 0);
		},
		imgLoad: function (url, callback) {
			var oimg = document.createElement("img");
			oimg.src = url;
			var timer = setInterval(function () {
				if (oimg.complete) {
					if (typeof callback != "undefined") {
						callback(oimg)
					}
					clearInterval(timer)
				}
			}, 50)
		},
		html5PlayerSkin: function (params, video, ts) {
			(function () {
				// player skin
				//测试环境 修改css地址 https://p.bokecc.com/css/html5player/ skin_pc.css?v6.2
				var css = document.createElement('link');
				css.classList.add('ccH5vidoeLinkTag')
				css.href = 'https://p.bokecc.com/css/html5player/skin_pc.css?v6.2';
				if (ts.isIPad() || ts.isWindowsPhoneOS()) {
					css.href = 'https://p.bokecc.com/css/html5player/skin2.css?v6.2';
					if (navigator.userAgent.match('8')) {
						css.href = 'https://p.bokecc.com/css/html5player/skin2_8.0.css?v6.2';
					}
				}
				if (ts.isAndroid() || ts.isIPhone()) {
					css.href = 'https://p.bokecc.com/css/html5player/skin_Android.css?v6.2';
					if (video.uid == "231986") {
						css.href = 'https://p.bokecc.com/css/html5player/canon_Android.css?v6.2';
					}
					//中意人寿皮肤定制
					if (video.uid == "240837") {
						css.href = 'https://p.bokecc.com/css/html5player/zyrs_Android.css?v6.2';
					}
				}
				if (ts.isIPad() && video.uid == "231986") {
					css.href = 'https://p.bokecc.com/css/html5player/canon_ipad.css?v6.2';
				}
				css.rel = "stylesheet";
				css.type = "text/css";
				document.head.appendChild(css);
			})();

			function CCHtml5Player() { };
			CCHtml5Player.prototype = {
				addListener: function (element, type, handler) {
					element.addEventListener(type, handler, false);
				},
				init: function (id) {
					//对copies数组过滤，分开audio和video
					var arr = video.copies;
					//过滤video
					if (video.copies[0].mediatype !== 2) {
						var videoArr = video.copies.filter(function (v) { return v.mediatype === 1 });
						//这样copies和以前的内容是一致的，只有video
						video.copies = videoArr;
					}
					// var videoArr = arr.filter(function(v) { return v.mediatype === 1});
					//过滤audio
					var audioArr = arr.filter(function (v) { return v.mediatype === 2 });
					//新增audio数组
					this.audioArr = audioArr;

					//检测线路的定时器 
					var checkStateTimer = null;
					//audio
					this.moreBtn = document.getElementsByClassName('ccH5More')[0]
					this.switchAudioBtn = document.getElementById('ccH5SwitchAudio');
					//线路切换
					this.isUseBackupUrl = false;
					this.switchUrlBtn = document.getElementById('ccH5SwitchUrl');
					this.btnList = document.getElementById('ccBtnList');
					// //字幕切换
					// this.switchVtt = document.getElementById('ccH5switchVtt');
					// this.switchVttBox = document.getElementById('switchVttBox');
					// this.switchVttContainer = this.switchVttBox.getElementsByTagName('ul')[0];
					// this.track  = this.oDiv.querySelector('track');
					//获取上次播放的时间
					this.historyTime = parseInt(localStorage.getItem(params.vid));
					//修复切换时总时间会显示错误的问题
					this.fixTotalTime = function (currentTime) {
						//修复iphone的bugal
						if (ts.isIPhone()) {
							_this.oVideo.play();
						}
						var fixTotalTime = function () {
							_this.oVideo.removeEventListener('canplay', fixTotalTime, false);

							_this.oVideo.currentTime = currentTime;
							_this.oVideo.pause();
							setTimeout(function () {
								_this.oVideo.play();
							}, 300);

						}
						_this.addListener(_this.oVideo, 'canplay', fixTotalTime);
					}
					this.showTheBtnList = function () {
						_this.btnList.style.display = 'block';
						_this.showBtnList = true;
					}
					this.hideTheBtnList = function () {
						_this.btnList.style.display = 'none'
						_this.showBtnList = false;
					}
					//
					this.audio = document.getElementsByTagName("audio")[0];
					this.switchVideoBtn = document.getElementsByClassName('ccbgButton')[0];
					this.isInVideo = true;
					//改为控制列表的显影
					this.showBtnList = false;
					this.showMoreBtn = function () {
						//假如是音频播放
						if (params.mediatype == 2) {
							return false;
						}
						//包含undefined的情况只能这样写了 当auidoArr不存在当没有备用线路时不显示moreBtn
						if (!(this.audioArr.length > 0) && video.copies.length > 0 && video.copies[0].backupurl === undefined) {
							return false;
						}
						return true;
					}
					// ++
					//记录hdul中li的位置
					this.currentQualityIdx = null;
					//记录audio清晰度的位置 
					this.currentAudioQualityIdx = null;
					//记录当前播放速度
					this.currentSpeedIdx = null;
					//
					this.oDiv = document.getElementById(id);
					this.oVideo = this.oDiv.getElementsByTagName("video")[0];
					this.adSrc = this.oDiv.getElementsByClassName("skipGg")[0];
					this.adcloseTime = this.oDiv.getElementsByClassName("closeTime")[0];
					this.imgCloseTime = this.oDiv.getElementsByClassName("closePicTime")[0];
					this.picAdSrc = this.oDiv.getElementsByClassName("picBtn")[0];
					this.vadSrc = this.oDiv.getElementsByClassName("vadSrc")[0];
					this.playBtn = this.oDiv.getElementsByClassName("ccH5PlayBtn")[0];
					this.playBtn2 = this.oDiv.getElementsByClassName("ccH5PlayBtn2")[0];
					this.videoBox = this.oDiv.getElementsByClassName("ccH5playerBox")[0];
					this.oLoading = this.oDiv.getElementsByClassName("ccH5Loading")[0];
					this.oInfo = this.oDiv.getElementsByClassName("ccH5Info")[0];
					this.poster = this.oDiv.getElementsByClassName("ccH5Poster")[0];
					this.ctrlBar = this.oDiv.getElementsByTagName("section")[0];
					this.loadBar = this.oDiv.getElementsByClassName("ccH5LoadBar")[0];
					this.toggleBtn = this.oDiv.getElementsByClassName("ccH5TogglePlay")[0];
					this.progressBar = this.oDiv.getElementsByClassName("ccH5ProgressBar")[0];
					this.dragBtn = this.oDiv.getElementsByClassName("ccH5DragBtn")[0];
					this.timeCurrent = this.oDiv.getElementsByClassName("ccH5TimeCurrent")[0];
					this.timeTotal = this.oDiv.getElementsByClassName("ccH5TimeTotal")[0];
					this.proCurrent = this.oDiv.getElementsByClassName("ccH5CurrentPro")[0];
					this.fullsBtn = this.oDiv.getElementsByClassName("ccH5FullsBtn")[0];
					this.exitFullsBtn = this.oDiv.getElementsByClassName("ccH5ExitFullsBtn")[0];
					this.hdBtn = this.oDiv.getElementsByClassName("ccH5hd")[0];
					this.hdUL = this.oDiv.getElementsByClassName("ccH5hdul")[0];
					this.spBtn = this.oDiv.getElementsByClassName("ccH5sp")[0];
					this.spUL = this.oDiv.getElementsByClassName("ccH5spul")[0];
					this.spLi = this.spUL.getElementsByTagName("li");
					this.vmBtn = this.oDiv.getElementsByClassName("ccH5vm")[0];
					this.vmDiv = this.oDiv.getElementsByClassName("ccH5vmdiv")[0];
					this.vmDragBtn = this.oDiv.getElementsByClassName("ccH5DragVmBtn")[0];
					this.vmBar = this.oDiv.getElementsByClassName("ccH5vmbar")[0];
					this.vmPro = this.oDiv.getElementsByClassName("ccH5vmbarPro")[0];
					this.advert = this.oDiv.getElementsByClassName("x-advert")[0];
					this.skipAdBtn = this.oDiv.getElementsByClassName("closeAd")[0];
					this.skipPicAd = this.oDiv.getElementsByClassName("closePicAd")[0];
					this.picAd = this.oDiv.getElementsByClassName("picAd")[0];
					this.xAdSec = this.oDiv.getElementsByClassName("x-advert-sec")[0];
					this.waitTime = this.oDiv.getElementsByClassName("skipNum")[0];
					this.waitPicTime = this.oDiv.getElementsByClassName("skipPicNum")[0];
					this.adSec = this.oDiv.getElementsByClassName("adSec")[0];
					this.pSrc = this.oDiv.getElementsByClassName("pSrc")[0];
					this.picTxth = this.oDiv.getElementsByClassName("picTxth")[0];
					this.audioBg = this.oDiv.getElementsByClassName("ccH5AudioBg")[0];
					this.videoMute = false;
					this.queryAnswerTimer = null;
					//字幕切换
					this.switchVtt = document.getElementById('ccH5switchVtt');
					this.switchVttBox = document.getElementById('switchVttBox');
					this.switchVttContainer = this.switchVttBox.getElementsByTagName('ul')[0];
					this.track = this.oDiv.querySelector('track');
					//微信全屏标记
					this.isEnterFullscreen = false;
					this.wechatCount = 0;
					// 历史播放时间容器
					this.historyTimeBox = this.oDiv.querySelector('#ccH5historyTimeBox');
					// 预览时间
					this.preViewTime = this.oDiv.querySelector('#cch5CurrentTime');
					if (!!video.marquee) {
						//跑马灯 创建容器
						this.marqueeBox = document.createElement('div');
						//当前的action idx
						this.currentActionIdx = 0;
						//循环
						this.marqueeLoop = video.marquee.loop;
					}
					(function (i) {
						if (!isNaN(params.width) || !isNaN(params.height)) {
							i.videoBox.style.width = params.width + 'px';
							i.videoBox.style.height = params.height + 'px';
							if (video.vrmode == 1 && (ts.isIPad() || ts.isIPhone() || ts.isAndroid())) {
								i.videoBox.style.width = window.innerWidth + 'px';
								i.videoBox.style.height = window.innerHeight + 'px';
								i.oVideo.style.width = window.innerWidth + 'px';
								i.oVideo.style.height = window.innerHeight + 'px';
							}
							//测试环境
							// if (ts.isMoble()) {
							// 	i.videoBox.style.width = '100vw';
							// 	i.videoBox.style.height = '200px';
							// 	i.oVideo.style.width = '100vw';
							// 	i.oVideo.style.height = '200px';				
							// }
						} else {
							i.videoBox.style.width = params.width;
							i.videoBox.style.height = params.height;
						}
					})(this);
					if (ts.isAndroid() || ts.isIPhone()) {
						this.ctrlWidth = this.videoBox.clientWidth - 247; //230 进度条的宽度 = 盒子宽 - （进度条left + right的值）这样进度条就可以做到宽度自适应
						//中意人寿皮肤定制
						if (video.uid == "240837") {
							this.ctrlWidth = this.videoBox.clientWidth - 214;
						}
					} else if (ts.isIPad()) {
						this.ctrlWidth = this.videoBox.clientWidth - 372;
					} else if (ts.isIPad() && video.uid == "231986") {
						//佳能ipad皮肤定制
						this.ctrlWidth = this.videoBox.clientWidth - 220;
					} else {
						this.ctrlWidth = this.videoBox.clientWidth;// - 378;
					}

					var _this = this;
					//切换线路 不能出现的情况 修改bug防止copies出现空的情况，执行之前的判断会报错 
					if (video.copies.length === 0 || !video.copies[ts.currentCopiesIdx].backupurl) {
						this.switchUrlBtn.style.display = "none";
					}
					// 切换线路的点击事件
					this.addListener(this.switchUrlBtn, 'click', function () {
						//切换前缓存 播放时间
						var currentTime = _this.oVideo.currentTime;
						var currentSpeedIdx = _this.findSpeedIdx()
						if (!_this.isUseBackupUrl) {
							_this.oVideo.src = video.copies[ts.currentCopiesIdx].backupurl.replace(/http:/i, "https:");
							_this.fixTotalTime(currentTime);
							_this.isUseBackupUrl = true;
						} else {
							//切回原播放地址
							_this.oVideo.src = video.copies[ts.currentCopiesIdx].playurl.replace(/http:/i, "https:");
							_this.fixTotalTime(currentTime);
							_this.isUseBackupUrl = false;
						}
						_this.spUL.getElementsByTagName('li')[currentSpeedIdx].click();
					})
					//audio不能出现的情况
					if (!(this.audioArr.length > 0)) {
						this.switchAudioBtn.style.display = 'none';
					}
					//audio
					this.addListener(this.switchAudioBtn, 'click', function () {
						//缓存播放时间
						var currentTime = 0;
						//缓存 video的高度
						var videoHeight = 0;
						//如果是在视频界面则切换到音频界面
						//切换到音频
						//记录spul中li的位置
						_this.currentSpeedIdx = _this.findSpeedIdx();
						if (_this.isInVideo) {
							//记录hdul中li的位置
							_this.currentQualityIdx = _this.findQualityIdx();
							//获取视频高度，给videobox赋值，防止videobox没有高度
							videoHeight = _this.oVideo.clientHeight;
							_this.oVideo.style.display = "none";
							_this.audioBg.style.display = "block";
							// 当移动端时默认宽高是100%，video消失后高度会 = 0；只能后续添加回去
							_this.oVideo.parentNode.style.height = videoHeight + "px";
							_this.oVideo.pause();
							//缓存视频当前的时间
							currentTime = _this.oVideo.currentTime;
							_this.oVideo = _this.audio;
							_this.oVideo.currentTime = currentTime;
							//切换清晰度列表
							_this.isInVideo = false;
							_this.setAudioQuality();
							//切换之前缓存的音频清晰度
							if (_this.currentAudioQualityIdx !== null) {
								//说明不是第一次进入音频界面，已经选择了音频清晰度，使用缓存的清晰度
								_this.hdUL.getElementsByTagName('li')[_this.currentAudioQualityIdx].click();
							}
							//切换之前缓存的播放速度
							_this.spUL.getElementsByTagName('li')[_this.currentSpeedIdx].click();
							//修复iphone 5c 无法使用同步代码直接控制播放的bug
							setTimeout(function () {
								_this.oVideo.play()
							}, 300);
							//改变按钮的文案
							_this.switchAudioBtn.innerText = "视频播放"
							//切换线路隐藏
							_this.switchUrlBtn.style.display = 'none';
						} else {
							//记录hdul中li的位置
							//切换到视频
							//此时 _this.oVideo 还是 _this.audio
							//缓存audio的清晰度
							_this.currentAudioQualityIdx = _this.findQualityIdx();
							//背景消失
							_this.audioBg.style.display = "none";
							//停止播放并缓存当前播放时间

							_this.oVideo.pause();
							currentTime = _this.oVideo.currentTime;
							//吧ovideo换回video
							_this.oVideo = document.getElementsByTagName("video")[0];
							//视频出现
							_this.oVideo.style.display = "block";
							//定位到缓存的播放时间
							if (!_this.oVideo.currentTime) {
								//修复iphone 5c会从头开始的bug
								// 音频时间为0时不进行切换
								//++ 修改为当前为0时切换回去是1
								_this.oVideo.currentTime = 1;
							} else {
								_this.oVideo.currentTime = currentTime;
							}
							//显示高清按钮
							// _this.hdBtn.style.display = "block";
							_this.setQuality(video)
							//切换回之前的清晰度
							_this.isInVideo = true;
							_this.hdUL.getElementsByTagName('li')[_this.currentQualityIdx].click();
							//切换之前缓存的播放速度
							_this.spUL.getElementsByTagName('li')[_this.currentSpeedIdx].click();
							//改变按钮的文案
							_this.switchAudioBtn.innerText = "音频播放";
							//切换线路出现 只有在备用线路存在的时候才能出现
							if (video.copies[ts.currentCopiesIdx].backupurl) {
								_this.switchUrlBtn.style.display = 'block';
							}
						}
						_this.btnList.style.display = "none";
					});
					//audio more
					this.addListener(this.moreBtn, 'click', function () {
						this.className = 'ccH5More ccH5FadeIn';
						if (!_this.showBtnList) {
							_this.showTheBtnList();
						} else {
							_this.hideTheBtnList();
						}
					})
					//audio more 不能出现的情况
					if (!this.showMoreBtn()) {
						this.moreBtn.style.display = 'none';
					}

					//只有音频的时候不显示切换到视频按钮
					if (params.mediatype == 2) {
						this.switchVideoBtn.style.display = 'none';
						//只有音频时直接进入audio
						this.switchAudioBtn.click();
					}
					//audio 切换到视频按钮的点击事件 this.switchVideoBtn
					this.addListener(this.switchVideoBtn, 'click', function () {
						_this.isInVideo = false;
						_this.switchAudioBtn.click();
					})
					this.addListener(this.playBtn, 'click', function () { _this.play(); });
					// this.addListener(this.playBtn2, 'click', function () { _this.play2(); });
					this.addListener(this.toggleBtn, 'click', function () { _this.togglePlay(); });
					this.addListener(this.skipAdBtn, 'click', function () { _this.skipAd(); });
					this.addListener(this.skipPicAd, 'click', function () { _this.skipAd(); });

					if (!!video.questions) {
						//this.audio
						//修复 ios全屏下拖动进度条可以跳过答题的bug,使用timeupdate事件对答题点进行查询
						this.addListener(this.oVideo, 'timeupdate', function () { _this.timeupdate(); _this.monitorQuestion();/*_this.queryAnswer(); */ });
						this.addListener(this.oVideo, 'playing', function () { _this.playing(); /*_this.queryAnswer();*/ });
						this.oVideo.addEventListener('timeupdate', this.queryAnswer.bind(this));
					} else {
						this.addListener(this.oVideo, 'timeupdate', function () { _this.timeupdate(); });
						this.addListener(this.oVideo, 'playing', function () { _this.playing(); });
						//audio
						this.addListener(this.audio, 'timeupdate', function () { _this.timeupdate(); });
						this.addListener(this.audio, 'playing', function () { _this.playing(); });
					}
					//跑马灯
					if (!!video.marquee) {
						this.oVideo.addEventListener('playing', this.createMarquee)
						//进入退出全屏要重新创建
						document.addEventListener('webkitfullscreenchange', _this.createMarquee)
					}
					this.addListener(this.fullsBtn, 'click', function () { _this.fullScreen(_this.videoBox); });
					this.addListener(this.exitFullsBtn, 'click', function () { _this.exitFullScreen(); });
					this.addListener(this.oVideo, 'pause', function () { _this.pause(); });
					this.addListener(this.oVideo, 'progress', function () { _this.progress(); });
					this.addListener(this.oVideo, 'ended', function () { _this.ended(); });
					// this.addListener(this.oVideo, 'webkitendfullscreen', function () { _this.endFullscreen(); });
					this.addListener(window, 'orientationchange', function () { _this.autoProgressBar(); });
					this.addListener(this.oVideo, 'seeking', function () { _this.seeking(); });
					this.addListener(this.oVideo, 'seeked', function () { _this.seeked(); });
					this.addListener(this.oVideo, 'waiting', function () { _this.waiting(); });
					this.addListener(this.oVideo, 'canplay', function () { _this.canplay(); });
					this.addListener(this.oVideo, 'gesturechange', function (e) {
						if (e.scale > 1) {
							_this.fullScreen();
						}
					});
					this.addListener(this.oVideo, 'touchstart', function (e) {
						//视音频切换 苹果不允许没有交互下预先加载视音频；
						//ios
						_this.audio.load()
						if (e.touches.length == 2) {
							_this.ctrlBar.className = "ccH5FadeIn";
							_this.oVideo.ontouchend = function () {
								_this.togglePlay();
								_this.oVideo.ontouchend = null;
							};
						}
					});
					this.addListener(this.oVideo, 'touchend', function (e) {
						if (e.changedTouches.length == 1) {
							_this.toggleCtrlBar();
						}
					});
					this.addListener(this.audioBg, 'touchend', function (e) {
						if (e.changedTouches.length == 1) {
							_this.toggleCtrlBar();
						}
					});

					if (ts.isRealtimePlay(video)) {
						this.hdBtn.style.display = "none";
					}

					// HD
					this.addListener(this.hdBtn, 'mouseover', function () { _this.toggleHd(); });
					this.addListener(this.hdBtn, 'mouseout', function () { _this.toggleHdHide(); });
					this.addListener(this.hdUL, 'mouseover', function () { _this.toggleHd(); });
					this.addListener(this.hdUL, 'mouseout', function () { _this.toggleHdHide(); });

					// playbackRate
					this.addListener(this.spBtn, 'mouseover', function () { _this.toggleSp(); });
					this.addListener(this.spBtn, 'mouseout', function () { _this.toggleSpHide(); });
					this.addListener(this.spUL, 'mouseover', function () { _this.toggleSp(); });
					this.addListener(this.spUL, 'mouseout', function () { _this.toggleSpHide(); });

					//mute
					this.addListener(this.vmBtn, 'click', function () { _this.mute(); });
					//选择默认字幕
					this.defaultVtt = this.selectedDefaultVtt.bind(this,video.defaultvtt);
					this.oVideo.addEventListener('timeupdate',this.defaultVtt);

					//changefullscreen
					this.fullscreenchange();

					//历史播放时间
					// this.oVideo.addEventListener('play',function() {
					// 	// console.log(_this.historyTime)
					// 	if (!isNaN(_this.historyTime)) {
					// 		_this.historyTimeBox.classList.add('fade');
					// 		var jumpInto = _this.historyTimeBox.querySelector('#ccH5jumpInto');
					// 		jumpInto.querySelector('i').innerHTML = _this.timeFormat(_this.historyTime)
					// 		_this.historyTimeBox.querySelector('#ccH5delBox').onclick = function() {
					// 			_this.historyTimeBox.classList.remove('fade');
					// 		}
					// 		jumpInto.onclick = function() {
					// 			_this.oVideo.currentTime = _this.historyTime;
					// 			_this.historyTimeBox.classList.remove('fade');
					// 		}
					// 	}
					// })
					this.oVideo.addEventListener('play', this.handleHistoryTime.bind(this));

					var s1 = 1;
					for (i = 0; i < 5; i++) {
						var _this = this;
						this.spLi[i].index = i;
						this.addListener(this.spLi[i], 'click', function () {
							var s = [2, 1.5, 1.2, 1, 0.8];
							for (j = 0; j < 5; j++) {
								_this.spLi[j].className = "";
							}
							this.className = "selected";

							if (ts.isIPad()) {
								_this.oVideo.pause();
							}
							_this.oVideo.playbackRate = s[this.index];

							var url = _this.oVideo.src;

							if (typeof window.changeSpeed === 'function') {
								window.changeSpeed(s1, _this.oVideo.playbackRate, url);
							}
							s1 = _this.oVideo.playbackRate;

							_this.spUL.style.display = 'none';
							_this.spBtn.style.background = 'rgba(51,51,51,0.5)';
							_this.spBtn.innerHTML = this.innerHTML;
							if (ts.isIPad()) {
								setTimeout(function () {
									_this.oVideo.play();
								}, 300)
							}
						});
					};

					this.setQuality(video);

					// vm
					var timer = null;
					function vmShow() {
						_this.vmDiv.style.display = 'block';
						clearTimeout(timer);
					};
					function vmHide() {
						timer = setTimeout(function () {
							_this.vmDiv.style.display = 'none';
						}, 300);
					};

					this.addListener(this.vmBtn, 'mouseover', vmShow);
					this.addListener(this.vmBtn, 'mouseout', vmHide);
					this.addListener(this.vmDiv, 'mouseover', vmShow);
					this.addListener(this.vmDiv, 'mouseout', vmHide);

					this.dragVm(this.vmDragBtn);

					if (typeof video.vtt != 'undefined' && video.vtt) {
						var css = 'video::cue{' + video.vtt.cssStyle + ' background: none; white-space:pre-wrap;}'
						this.addCssByStyle(css);
					}
					if (params.mediatype == 2) {
						if (ts.videoAd == 1) {
							this.audioBg.style.display = 'none';
						} else {
							this.audioBg.style.display = 'block';
						}
					}

					// video hls
					this.addListener(this.oVideo, 'loadstart', function () {
						if (_this.oVideo.src.indexOf('.m3u8') != -1 && !(ts.isIPad() || ts.isIPhone() || ts.isAndroid())) {
							function callbak() {
								var oVsrc = _this.oVideo.src;
								if (Hls.isSupported()) {
									var hls = new Hls();
									hls.loadSource(oVsrc);
									hls.attachMedia(_this.oVideo);
								}
							}
							if (typeof Hls === 'function') {
								callbak();
							} else {
								ts.loadScript("https://p.bokecc.com/js/player/hls.js", callbak);
							}
						}
					});
					//字幕切换
					//初始化按钮
					if ((/firefox/i).test(navigator.userAgent)) {
						this.switchVtt.style.display = 'none';
					}
					this.initSwitchVttBtns();
					//选取默认字幕
					// this.selectedDefaultVtt(video.defaultvtt);
					this.switchVtt.addEventListener('click', function () {
						_this.switchVttBox.style.visibility = 'visible';
						_this.switchVttBox.style.top = 0;
					})
					//video autoplay
					// 2019-1-16 自动播放改版
					if (params.autoStart == 'true' && !(ts.isIPad() || ts.isIPhone() || ts.isAndroid())) {
						this.playBtn.style.display = 'none';
						this.oLoading.style.display = 'block';
						this.poster.style.display = 'none';
						if (cc_js_Player.videoAd != 1 ) {
							this.toggleFade();
							var _this = this;
							this.drag(this.dragBtn);
							this.progressBar.ontouchstart = this.progressBar.onclick = function (e) {
								_this.posDuration(e);
								if (!!video.questions) {
									_this.queryAnswer();
								}
							};
						}
						// this.oVideo.onloadedmetadata = function () {
						// 	_this.oLoading.style.display = 'none';
						// 	_this.play();
						// }
					}
					//预览时间
					this.progressBar.addEventListener('mousemove', function (e) {
						// 边界限制
						var posX = e.clientX - 30;
						if (posX < 0) {
							posX = 0;
						}
						if (posX > this.clientWidth - 50) {
							posX = this.clientWidth - 50;
						}
						_this.preViewTime.style.left = posX + 'px';
						_this.preViewTime.style.display = 'block';
						//换算成当前时间
						percent = ((e.clientX - 7) / this.clientWidth).toFixed(2);
						currentTime = _this.timeFormat(parseInt(percent * _this.oVideo.duration));
						_this.preViewTime.querySelector('p').innerText = currentTime;
					})
					this.progressBar.addEventListener('mouseout', function (e) {
						_this.preViewTime.style.display = 'none';
					})
					window.onresize = function () {

						var boxWidth = _this.videoBox.style.width;
						var boxPos = _this.videoBox.style.position;
						var boxIndex = _this.videoBox.style.zIndex;
						var boxTextIndent = _this.videoBox.style.textIndent;
						var boxBackground = _this.videoBox.style.backgroundColor;
						if (boxWidth == '100%' && boxPos == 'fixed' && boxIndex == '9999' && boxTextIndent == '0px' && boxBackground == 'rgb(0, 0, 0)') {
							_this.videoBox.innerHTML = '视频不支持录屏模式下播放';
							_this.videoBox.style.color = '#ffffff';
							_this.videoBox.style.textAlign = 'center';
							_this.videoBox.style.lineHeight = '490px';
						}
						if (ts.isWeixin() && ts.isAndroid()) {
							++_this.wechatCount === 2 ? _this.isEnterFullscreen = true : _this.isEnterFullscreen = false;
						}
						if (_this.wechatCount === 4) {
							_this.wechatCount = 0;
						}
					}

					//检测线路
					if (video.copies.length > 0 && video.copies[0].backupurl !== undefined) {
						ts.checkVideoNetworkState(_this, video, checkStateTimer);
					}

				},
				// 历史播放记录事件处理
				handleHistoryTime: function () {
					var that = this;
					if (!isNaN(this.historyTime)) {
						this.historyTimeBox.classList.add('fade');
						//定时5秒后假如还存在则移除
						setTimeout(function() {
							that.oDiv.querySelector('#ccH5historyTimeBox') && that.videoBox.removeChild(that.historyTimeBox);
						}, 5000);
						var jumpInto = this.historyTimeBox.querySelector('#ccH5jumpInto');
						jumpInto.querySelector('i').innerHTML = this.timeFormat(this.historyTime)
						this.historyTimeBox.querySelector('#ccH5delBox').onclick = function () {
							that.videoBox.removeChild(that.historyTimeBox);
						}
						jumpInto.onclick = function () {
							that.oVideo.currentTime = that.historyTime;
							that.historyTimeBox.classList.remove('fade');
							that.videoBox.removeChild(that.historyTimeBox);
						}
					}
				},
				//字幕切换按钮初始化
				initSwitchVttBtns: function () {
					var lis = ''
					var count = 0
					if (video.vtt === undefined) {
						this.switchVtt.style.display = 'none';
						return;
					} else {
						lis += '<li id="vtt">' + video.vtt.vttName + '</li>';
						count++;
					}
					if (video.vtt2 !== undefined) {
						lis += '<li id="vtt2">' + video.vtt2.vttName + '</li>';
						count++;
					}
					if (video.bilingual !== undefined) {
						lis += '<li id="bilingual">' + video.bilingual.vttName + '</li>';
						count++;
					}
					lis += '<li id="closeVtt">关闭字幕</li>';
					this.switchVttContainer.innerHTML = lis;
					// var track = document.getElementsByTagName('track')[0];
					var btns = this.switchVttContainer.getElementsByTagName('li');
					var that = this;
					if (count > 1) {
						this.bindEvent('vtt', btns, this.track, that);
						this.bindEvent('vtt2', btns, this.track, that);
						this.bindEvent('bilingual', btns, this.track, that);
					} else {
						this.bindEvent('vtt', btns, this.track, that);
					}
					//关闭字幕
					document.getElementById('closeVtt').onclick = function () {
						for (var i = 0; i < btns.length; i++) {
							btns[i].className = '';
						}
						this.classList.add('active')
						that.track.src = '';
						that.switchVttBox.style.visibility = 'hidden';
						that.switchVttBox.style.top = '150%';
					}

				},
				bindEvent: function (id, btns, track, that) {
					document.getElementById(id).onclick = function () {
						for (var i = 0; i < btns.length; i++) {
							btns[i].className = '';
						}
						this.classList.add('active')
						track.src = video[id].vttUrl.replace(/http:/i, "https:");
						that.switchVttBox.style.visibility = 'hidden'
						that.switchVttBox.style.top = '150%';
						//改变字幕样式
						//添加前移除
						var style = document.getElementById('ccVttStyle')
						style.parentNode.removeChild(style);
						var css = 'video::cue{' + video[id].cssStyle + ' background: none; white-space:pre-wrap;}'
						that.addCssByStyle(css);
					}
				},
				selectedDefaultVtt: function (defaultvtt) {
					this.oVideo.removeEventListener('timeupdate',this.defaultVtt);
					if (defaultvtt === undefined || (/firefox/i).test(navigator.userAgent)) {
						return;
					}
					var obj = {
						'0': 'vtt',
						'1': 'vtt2',
						'2': 'bilingual'
					}
					//解决hls下默认字幕不出现的问题
					 this.track.src = '';
						var that = this;
					 setTimeout(function() {
						 console.log(that.oDiv.querySelector("#" + obj[defaultvtt]));
						that.oDiv.querySelector("#" + obj[defaultvtt]).click();
					}, 380);
				},

				//创建问题
				monitorQuestion: function () {
					if (cc_js_Player.isAdTime == true) {
						return;
					}
					var _this = this;
					var t = parseInt(_this.oVideo.currentTime);
					for (var i = 0; i < video.questions.length; i++) {
						if (!this.isQuestionsShow && !ts.ans['ans_' + video.questions[i].id] && video.questions[i].showTime == t) {
							var index = i;
							_this.oVideo.pause();
							if (_this.isEnterFullscreen) {
								alert("请退出全屏回答问题")
							}
							_this.exitFullScreen();

							this.isQuestionsShow = true;
							var arr = [];
							var num = 0;
							for (var j = 0; j < video.questions[i].answers.length; j++) {
								var questionAnswers = video.questions[i].answers[j].right;
								if (questionAnswers == true) {
									num++;
								}
								arr.push(questionAnswers);
							}
							//新增 提示多选
							if (num > 1) {
								this.checkboxSelect(i);
							} else {
								this.radioSelect(i);
							}
							var confirm = document.getElementById('ccQuestionSubmit');
							confirm.onclick = function () {
								var sInp = document.getElementsByClassName('ccInputBox')[0].getElementsByTagName('input');
								var inputArr = [];
								var answerId = [];
								var checked = 0;
								for (var i = 0; i < sInp.length; i++) {
									inputArr.push(sInp[i].checked)
									if (sInp[i].checked) {
										answerId.push(video.questions[index].answers[i].id)
										answerId.join(',');
										checked++;
									}
								}

								var answerStatus;
								if (checked == 0) {
									_this.createQuestionTips();
								} else if (!cmpare(arr, inputArr)) {
									_this.createWrongAnswer(video.questions[index].explainInfo, index);
									answerStatus = 0;
								} else {
									_this.createRightAnswer(video.questions[index].explainInfo, index);
									answerStatus = 1;
								}
								var timestamp = new Date().getTime();
								if (checked != 0 && !ts.answerd['first_' + video.questions[index].id]) {
									cc_js_Player.jsonp("https://ikkyu.bokecc.com/servlet/report?vid=" + param.vid + "&qid=" + video.questions[index].id + "&answer=" + answerId + "&status=" + answerStatus + "&terminal_type=30" + "&time=" + timestamp);
									ts.answerd['first_' + video.questions[index].id] = true;
								}
							};
							var ccJumpOver = document.getElementById('ccJumpOver');
							var ccQuestionDiv = document.getElementsByClassName('ccQuestionDiv')[0];
							if (!video.questions[i].jump) {
								ccJumpOver.style.display = 'none';
							}
							ccJumpOver.onclick = function () {
								_this.videoBox.removeChild(ccQuestionDiv);
								_this.oVideo.play();
								ts.ans['ans_' + video.questions[index].id] = true;
								_this.isQuestionsShow = false;
							};
							function cmpare() {
								for (var i = 0; i < arguments.length; i++) {
									return arguments[i].toString() == arguments[i + 1] ? true : false;
								}
							}
						}
					}
				},
				queryAnswer: function () {
					//节流
					clearTimeout(this.queryAnswerTimer);
					var _this = this;
					setTimeout(function () {
						var t = _this.oVideo.currentTime;
						var distime = 0;
						for (var i = 0; i < video.questions.length; i++) {
							if (!ts.ans['ans_' + video.questions[i].id]) {
								var imShowTime = video.questions[i].showTime;
								if (t >= imShowTime) {
									distime = imShowTime;
									break;
								}
							}
						}
						if (distime > 0) {
							_this.oVideo.pause();
							_this.oVideo.currentTime = distime;
						}
					}, 1000)
				},
				//创建问答弹框
				createQuestionBox: function (text) {

					var oImg;
					if (ts.isIPhone() || ts.isAndroid()) {
						oImg = 'https://p.bokecc.com/images/questionimage/ccquestionbgphone.jpg';
					} else {
						oImg = 'https://p.bokecc.com/images/questionimage/ccquestionbg.png';
					}
					return "<div class='ccQuestionBox'>"
						+ "  <img class='ccQuestionBg' src='" + oImg + "'>"
						+ "  <div class='ccQuestion'>"
						+ "  	<div class='ccProblem'><span class='ccCheckTips fl'>单选</span><span class='fl tim'>题目:</span><span class='text fl'>" + text + "</span></div>"
						+ "     <ul class='ccQuestionList'></ul>"
						+ "     <div class='ccInputBox'></div>"
						+ "  </div>"
						+ "  <input id='ccJumpOver' type='button' value='跳过'>"
						+ "  <input id='ccQuestionSubmit' type='submit' value='提交'>"
						+ "</div>";
				},
				radioSelect: function (num) {
					var _this = this;
					var oDiv = document.createElement('div');
					var oText = video.questions[num].content.replace(/\{([^\{\}]*)\}/g, '<img src="$1"/>');
					oText = oText.replace(/<img src="http:\/\/([^\"]+)bokecc.com([^\"]+)"\/>/g, '<img src="https://$1bokecc.com$2"/>');
					oDiv.className = 'ccQuestionDiv';
					oDiv.innerHTML = this.createQuestionBox(oText);
					_this.videoBox.appendChild(oDiv);

					for (var i = 0; i < video.questions[num].answers.length; i++) {
						var oUl = document.getElementsByClassName('ccQuestionList')[0];
						var ccInputBox = document.getElementsByClassName('ccInputBox')[0];
						var List = document.createElement('li');
						var liText = video.questions[num].answers[i].content.replace(/\{([^\{\}]*)\}/g, '<img src="$1"/>');
						liText = liText.replace(/<img src="http:\/\/([^\"]+)bokecc.com([^\"]+)"\/>/g, '<img src="https://$1bokecc.com$2"/>');
						List.innerHTML = "<i class='radioBg'></i><span>" + liText + "</span>";
						oUl.appendChild(List);
						var sInputRadio = document.createElement('input');
						sInputRadio.type = 'radio';
						ccInputBox.appendChild(sInputRadio);
					}

					var sLi = document.getElementsByClassName('ccQuestionList')[0].getElementsByTagName('li');
					var sInput = document.getElementsByClassName('ccInputBox')[0].getElementsByTagName('input');
					for (var i = 0; i < sLi.length; i++) {
						sLi[i].index = i;
						sLi[i].onclick = function () {
							for (var j = 0; j < sLi.length; j++) {
								sLi[j].classList.remove('active');
								sInput[j].removeAttribute('checked');
							}
							this.classList.add('active');
							sInput[this.index].setAttribute('checked', true);
						}
					}
					oDiv.getElementsByClassName('ccCheckTips')[0].innerText = '单选';
				},
				checkboxSelect: function (num) {
					var _this = this;
					var oDiv = document.createElement('div');
					var oText = video.questions[num].content.replace(/\{([^\{\}]*)\}/g, '<img src="$1"/>');
					oText = oText.replace(/<img src="http:\/\/([^\"]+)bokecc.com([^\"]+)"\/>/g, '<img src="https://$1bokecc.com$2"/>');
					oDiv.className = 'ccQuestionDiv';
					oDiv.innerHTML = this.createQuestionBox(oText);
					_this.videoBox.appendChild(oDiv);

					for (var i = 0; i < video.questions[num].answers.length; i++) {
						var oUl = document.getElementsByClassName('ccQuestionList')[0];
						var ccInputBox = document.getElementsByClassName('ccInputBox')[0];
						var List = document.createElement('li');
						var liText = video.questions[num].answers[i].content.replace(/\{([^\{\}]*)\}/g, '<img src="$1"/>');
						liText = liText.replace(/<img src="http:\/\/([^\"]+)bokecc.com([^\"]+)"\/>/g, '<img src="https://$1bokecc.com$2"/>');
						List.innerHTML = "<i class='checkboxBg'></i><span>" + liText + "</span>";
						oUl.appendChild(List);
						var sInputCheckbox = document.createElement('input');
						sInputCheckbox.type = 'checkbox';
						ccInputBox.appendChild(sInputCheckbox);
					}

					var sLi = document.getElementsByClassName('ccQuestionList')[0].getElementsByTagName('li');
					var sInput = document.getElementsByClassName('ccInputBox')[0].getElementsByTagName('input');
					for (var i = 0; i < sLi.length; i++) {
						sLi[i].index = i;
						sLi[i].onclick = function () {
							if (this.classList.contains('active') == true) {
								this.classList.remove('active');
								sInput[this.index].removeAttribute('checked');
							} else {
								this.classList.add('active');
								sInput[this.index].setAttribute('checked', true);
							}
						}
					}
					oDiv.getElementsByClassName('ccCheckTips')[0].innerText = '多选';
				},
				createQuestionTips: function () {
					var ccQuestionBox = document.getElementsByClassName('ccQuestionBox')[0];
					var ccTips = document.createElement('div');
					ccTips.className = 'ccTips'
					ccTips.innerHTML = "<span>请选择答案</span><div class='dark'></div>";
					ccQuestionBox.appendChild(ccTips);
					setTimeout(function () {
						ccQuestionBox.removeChild(ccTips);
					}, 3000);
				},
				createWrongAnswer: function (txt, index) {
					var _this = this;
					var ccQuestionDiv = document.getElementsByClassName('ccQuestionDiv')[0];
					var ccQuestionBox = document.getElementsByClassName('ccQuestionBox')[0];
					var wrongTips = document.createElement('div');
					var oImg;
					if (ts.isIPhone() || ts.isAndroid()) {
						oImg = 'https://p.bokecc.com/images/questionimage/wronganswerphone.jpg';
					} else {
						oImg = 'https://p.bokecc.com/images/questionimage/wronganswer.png';
					}
					wrongTips.className = 'ccWrongTips'
					wrongTips.innerHTML =
						"<div class='dark'></div><div class='ccWrongAnswer'><img src='" + oImg + "' alt=''><div class='txt'><p>" +
						txt + "</p></div><input type='submit' id='wrongBtn' value='确认'></div>";
					ccQuestionBox.appendChild(wrongTips);

					var wrongBtn = document.getElementById('wrongBtn');
					var backTime = video.questions[index].backSecond;
					var a = index;
					if (backTime != -1) {
						wrongBtn.value = '返回';
					}
					wrongBtn.onclick = function () {
						if (backTime == -1) {
							_this.videoBox.removeChild(ccQuestionDiv);
							_this.oVideo.play();
							ts.ans['ans_' + video.questions[a].id] = true;
							_this.isQuestionsShow = false;
						} else {
							_this.videoBox.removeChild(ccQuestionDiv);
							_this.oVideo.currentTime = backTime;
							_this.oVideo.play();
							_this.isQuestionsShow = false;
						}
					};
				},
				createRightAnswer: function (txt, index) {
					var _this = this;
					var ccQuestionDiv = document.getElementsByClassName('ccQuestionDiv')[0];
					var ccQuestionBox = document.getElementsByClassName('ccQuestionBox')[0];
					var rightTips = document.createElement('div');
					var oImg;
					if (ts.isIPhone() || ts.isAndroid()) {
						oImg = 'https://p.bokecc.com/images/questionimage/rightanswerphone.jpg';
					} else {
						oImg = 'https://p.bokecc.com/images/questionimage/rightanswer.png';
					}
					rightTips.className = 'ccRightTips';
					rightTips.innerHTML =
						"<div class='dark'></div><div class='ccRightAnswer'><img src='" + oImg + "' alt=''><div class='txt'><p>" +
						txt + "</p></div><input type='submit' id='rightBtn' value='确认'></div>";
					ccQuestionBox.appendChild(rightTips);

					var a = index;
					var rightBtn = document.getElementById('rightBtn');
					rightBtn.onclick = function () {
						_this.videoBox.removeChild(ccQuestionDiv);
						_this.oVideo.play();
						ts.ans['ans_' + video.questions[a].id] = true;
						_this.isQuestionsShow = false;
					};
				},
				autoProgressBar: function () {
					var _this = this;
					setTimeout(function () {
						if (ts.isAndroid() || ts.isIPhone()) {
							_this.ctrlWidth = _this.videoBox.clientWidth - 247; //218
						} else if (ts.isIPad()) {
							_this.ctrlWidth = _this.videoBox.clientWidth - 372;
						} else {
							_this.ctrlWidth = _this.videoBox.clientWidth;
						}
						_this.progress();
						_this.timeupdate();
					}, 300);
				},
				openBarrage: function (size, col, dur) {
					ts.CCStyle = { "size": 20, "col": 0xffffff, "dur": 10000 };
					if (typeof size != 'undefined' && size != null) {
						ts.CCStyle.size = size;
					}
					if (typeof col != 'undefined' && col != null) {
						if (typeof col == 'string' && col.charAt(0) == '#') {
							col = '0x' + col.substring(1);
							col = parseInt(col, '0x');
						}
						ts.CCStyle.col = col;
					}
					if (typeof dur != 'undefined' && dur != null) {
						ts.CCStyle.dur = dur;
					}
					if (ts.hasOpenBarrage) {
						return;
					}
					ts.hasOpenBarrage = true;
					var barragebox = document.createElement('div');
					barragebox.innerHTML = this.createBarrageBox();
					this.videoBox.insertBefore(barragebox, this.oVideo);
					ts.loadScript("https://p.bokecc.com/js/player/CommentCoreLibrary.js", function () {
						ts.CM = new CommentManager(document.getElementById('ccBarrage'));
						ts.CM.init(); // 初始化
						ts.CM.start();
					});
				},
				sendBarrage: function (mtext, size, col, dur) {
					if (!ts.hasOpenBarrage) {
						this.openBarrage();
						return;
					}
					if (!ts.CM) {
						return;
					}
					if (typeof mtext == "undefined") {
						return;
					}
					if (typeof col == 'string' && col.charAt(0) == '#') {
						col = '0x' + col.substring(1);
						col = parseInt(col, '0x');
					}
					col = parseInt(col);
					ts.CM.send({
						"mode": 1,
						"text": mtext,
						"size": size || ts.CCStyle.size,
						"color": col || ts.CCStyle.col,
						"dur": dur || ts.CCStyle.dur
					});
				},
				showBarrage: function () {
					if (!ts.hasOpenBarrage) {
						this.openBarrage();
						return;
					}
					document.getElementsByClassName("abp")[0].style.visibility = "visible";
				},
				hideBarrage: function () {
					if (!ts.hasOpenBarrage) {
						return;
					}
					document.getElementsByClassName("abp")[0].style.visibility = "hidden";
				},
				createBarrageBox: function () {
					return '<div class="abp" style="position:absolute; width:100%; height:100%; z-index:1;"><div id="ccBarrage" class="container" style="perspective: 227.217px;"></div></div>';
				},
				fullscreenchange: function () {
					var _this = this;
					var full = false;
					var list = ['fullscreenchange', 'mozfullscreenchange', 'webkitfullscreenchange', 'msfullscreenchange'];
					for (i in list) {
						var type = list[i];
						document.addEventListener(type, function (e) {
							full = !full;
							if (full == false) {
								_this.exitFullsBtn.click();
							}
						});
					}
				},
				addCssByStyle: function (cssString) {
					var doc = document;
					var style = doc.createElement("style");
					style.setAttribute("type", "text/css");
					style.id = 'ccVttStyle'
					var cssText = doc.createTextNode(cssString);
					style.appendChild(cssText);
					var heads = doc.getElementsByTagName("head");
					if (heads.length) {
						heads[0].appendChild(style);
					} else {
						doc.documentElement.appendChild(style);
					}
				},
				toggleHd: function () {
					this.hdUL.style.display = 'block';
					this.hdBtn.style.background = 'rgba(255,146,10,0.6)';
					this.spUL.style.display = 'none';
					this.spBtn.style.background = 'rgba(51,51,51,0.5)';
					clearTimeout(window.hdtimer);
				},
				toggleHdHide: function () {
					var _this = this;
					hdtimer = setTimeout(function () {
						_this.hdUL.style.display = 'none';
						_this.hdBtn.style.background = 'rgba(51,51,51,0.5)';
					}, 200);
				},
				toggleSp: function () {
					this.spUL.style.display = 'block';
					this.spBtn.style.background = 'rgba(255,146,10,0.6)';
					this.hdUL.style.display = 'none';
					this.hdBtn.style.background = 'rgba(51,51,51,0.5)';
					clearTimeout(window.sptimer);
				},
				toggleSpHide: function () {
					var _this = this;
					sptimer = setTimeout(function () {
						_this.spUL.style.display = 'none';
						_this.spBtn.style.background = 'rgba(51,51,51,0.5)';
					}, 200);
				},
				//新增 查找当前选中清晰度的idx
				findQualityIdx: function () {
					var lis = this.hdUL.getElementsByTagName('li');
					for (var i = 0; i < lis.length; i++) {
						if (lis[i].className === 'selected') {
							return i
						}
					}
				},
				findSpeedIdx: function () {
					var lis = this.spUL.getElementsByTagName('li');
					for (var i = 0; i < lis.length; i++) {
						if (lis[i].className === 'selected') {
							return i
						}
					}
				},
				setQuality: function (video) {
					//初始化hdul
					this.hdUL.innerHTML = '';
					//计算音频清晰度出现的次数
					for (i = 0; i < video.copies.length; i++) {
						this.hdUL.style.top = -(video.copies.length) * 30 + 'px';
						var li = document.createElement("li");
						var liText = document.createTextNode(video.copies[i].desp);
						li.appendChild(liText);
						this.hdUL.appendChild(li);
						this.hdUL.style.top = -video.copies.length * 30 + 'px';
					}
					this.switchQuality();
				},
				//audio
				setAudioQuality: function () {
					//初始化hdul
					this.hdUL.innerHTML = '';
					//计算视频清晰度出现的次数
					for (i = 0; i < this.audioArr.length; i++) {
						this.hdUL.style.top = -(this.audioArr.length) * 30 + 'px';
						var li = document.createElement("li");
						var liText = document.createTextNode(this.audioArr[i].desp);
						li.appendChild(liText);
						this.hdUL.appendChild(li);
					}
					this.switchQuality();
					//选取默认的音频清晰度
					if (this.currentAudioQualityIdx === null) {
						this.hdUL.getElementsByTagName('li')[0].click();
					}
				},
				//切换清晰度
				switchQuality: function () {
					var hdLi = this.hdUL.getElementsByTagName("li");
					for (i = 0; i < hdLi.length; i++) {
						var _this = this;
						hdLi[i].index = i;
						if (hdLi[i].innerHTML == this.hdBtn.innerHTML) {
							hdLi[i].className = 'selected';
						}
						//audio
						if (hdLi.length >= 1) {
							this.addListener(hdLi[i], 'click', function () {
								for (i = 0; i < hdLi.length; i++) {
									hdLi[i].className = "";
								}
								this.className = 'selected';
								//优化 点击哪个就用那个的文本内容
								_this.hdBtn.innerHTML = this.innerText;
								// _this.hdBtn.innerHTML = video.copies[this.index].desp;
								_this.hdUL.style.display = 'none';
								_this.hdBtn.style.background = 'rgba(51,51,51,0.5)';

								var t = _this.oVideo.currentTime;
								var u1 = _this.oVideo.src;
								if (_this.isInVideo) {
									//记录当前copies的idx,切换哪个就记录哪个
									ts.currentCopiesIdx = this.index;
									_this.oVideo.src = video.copies[this.index].playurl.replace(/http:/i, "https:");
								} else {
									//++
									_this.oVideo.src = _this.audioArr[this.index].playurl.replace(/http:/i, "https:");
								}
								if (typeof window.get_custom_id === 'function') {
									var flow = encodeURIComponent(get_custom_id());
									if (_this.isInVideo) {
										ts.currentCopiesIdx = this.index;
										_this.oVideo.src = video.copies[this.index].playurl.replace(/http:/i, "https:") + '&custom_id=' + flow;
									} else {
										//audio
										_this.oVideo.src = _this.audioArr[this.index].playurl.replace(/http:/i, "https:");
									}
								}
								if (typeof window.changeQuality === 'function') {
									window.changeQuality(u1, _this.oVideo.src);
								}
								//2018-11-5 11:23:15 解决部分浏览器切换清晰度后无法自动播放的问题。
								_this.oVideo.play()
								_this.spBtn.innerHTML = _this.spLi[3].innerHTML;
								for (i = 0; i < 5; i++) {
									_this.spLi[i].className = "";
								}
								_this.spLi[3].className = "selected";

								var h = function () {
									_this.oVideo.removeEventListener('canplay', h, false);
									_this.setDura(t);
									_this.pause();
									setTimeout(function () {
										_this.play();
									}, 300);
								}

								_this.addListener(_this.oVideo, 'canplay', h);
							});
						}
					}
				},
				picAdStyle: function () {
					var _this = this;
					var picW = this.pSrc.width;
					var picH = this.pSrc.height;
					var vidW = this.oVideo.offsetWidth;
					var vidH = this.oVideo.offsetHeight;
					var picHeight = vidW * picH / picW;
					var padH = (vidH - picHeight) / 2 + 'px';
					if ((picW / picH) <= (vidW / vidH)) {
						_this.pSrc.style.height = '100%';
					} else {
						_this.pSrc.style.width = '100%';
						_this.pSrc.style.paddingTop = padH;
					}
				},
				videoTime: function () {
					this.oVideo.play();
					var _this = this;
					var s = cc_js_Player.adTime;
					this.advert.style.display = 'block';
					window.aaa = function () {
						s--;
						_this.xAdSec.innerHTML = s;
						if (s == 0) {
							clearInterval(timer);
							_this.skipAdBtn.click();
						}
					}
					this.addListener(this.oVideo, 'playing', function () {
						if (typeof timer === 'undefined') {
							timer = setInterval('aaa()', 1000);
						}

					});
				},
				videoTime2: function () {
					this.oVideo.play();
					var _this = this;
					var s = this.xAdSec.innerHTML;
					this.advert.style.display = 'block';
					window.ccc = function () {
						s--;
						_this.xAdSec.innerHTML = s;
						if (s == 0) {
							clearInterval(timer);
							_this.skipAdBtn.click();
						}
					}
					timer = setInterval('ccc()', 1000);
				},
				picAdTime: function () {
					var s = cc_js_Player.adTime;
					var _this = this;
					this.picAd.style.display = 'block';
					window.bbb = function () {
						s--;
						_this.adSec.innerHTML = s;
						if (s == 0) {
							clearInterval(timer);
							_this.skipAdBtn.click();
						}
					}
					timer = setInterval('bbb()', 1000);
				},
				waitCloseBtn: function () {
					var s = cc_js_Player.skipTime;
					var _this = this;
					this.adcloseTime.style.display = 'inline-block';
					this.skipAdBtn.style.pointerEvents = 'none';
					window.ddd = function () {
						s--;
						_this.waitTime.innerHTML = s;
						if (s == 0) {
							clearInterval(timer2);
							_this.adcloseTime.style.display = 'none';
							_this.skipAdBtn.style.pointerEvents = 'auto';
						}
					}
					timer2 = setInterval('ddd()', 1000);
				},
				waitPicClose: function () {
					var s = cc_js_Player.skipTime;
					var _this = this;
					this.imgCloseTime.style.display = 'inline-block';
					this.skipPicAd.style.pointerEvents = 'none';
					window.eee = function () {
						s--;
						_this.waitPicTime.innerHTML = s;
						if (s == 0) {
							clearInterval(timer2);
							_this.imgCloseTime.style.display = 'none';
							_this.skipPicAd.style.pointerEvents = 'auto';
						}
					}
					timer2 = setInterval('eee()', 1000);
				},
				play: function () {
					this.oVideo.play();
					if (cc_js_Player.videoAd == 1 && cc_js_Player.adPlay == true) {
						var format = cc_js_Player.adUrl;
						var pos = format.lastIndexOf('.');
						var lastN = format.substring(pos, format.length);
						cc_js_Player.adPlay = false;
						if (lastN.toLowerCase() == '.jpg' || lastN.toLowerCase() == '.png' || lastN.toLowerCase() == '.gif') {
							this.oVideo.pause();
							this.adSec.innerHTML = cc_js_Player.adTime;
							this.pSrc.src = cc_js_Player.picSrc;
							this.picAdSrc.href = cc_js_Player.getAdSrc();
							this.picTxth.href = cc_js_Player.getAdSrc();
							if (cc_js_Player.clickurl == '') {
								this.picAdSrc.style.display = 'none';
								this.picTxth.style.display = 'none';
							}
							this.picAdTime();
							if (cc_js_Player.skipTime != 0) {
								this.waitPicTime.innerHTML = cc_js_Player.skipTime;
								this.waitPicClose();
							}
						} else {
							this.xAdSec.innerHTML = cc_js_Player.adTime;
							this.adSrc.href = cc_js_Player.getAdSrc();
							this.vadSrc.href = cc_js_Player.getAdSrc();
							if (cc_js_Player.clickurl == '') {
								this.vadSrc.style.display = 'none';
								this.adSrc.style.display = 'none';
							}
							this.videoTime();
							if (cc_js_Player.skipTime != 0) {
								this.waitTime.innerHTML = cc_js_Player.skipTime;
								this.waitCloseBtn();
							}
						}
					}
					if (cc_js_Player.canskip == 1) {
						this.skipPicAd.style.display = 'block';
						this.skipAdBtn.style.display = 'block';
					} else {
						this.skipPicAd.style.display = 'none';
						this.skipAdBtn.style.display = 'none';
					}
					if (cc_js_Player.canClick == 1 && cc_js_Player.clickurl != '') {
						this.adSrc.style.display = 'block';
						this.picAdSrc.style.display = 'block';
					} else {
						this.adSrc.style.display = 'none';
						this.picAdSrc.style.display = 'none';
					}
					this.playBtn.style.display = 'none';
					this.poster.style.display = 'none';
					this.toggleBtn.className = "ccH5TogglePause";
					if (cc_js_Player.videoAd != 1 && !(ts.isAndroid() || ts.isIPhone() || ts.isIPad())) {
						this.toggleFade();
					}
					this.drag(this.dragBtn);
					//this.singleTouch(this.oVideo);
					var _this = this;
					this.progressBar.ontouchstart = this.progressBar.onclick = function (e) {
						_this.posDuration(e);
						if (!!video.questions) {
							_this.queryAnswer();
						}
					};

					if (video.authenable == 0) {
						if (video.freetime == 0) {
							var text = video.authmessage;
							if (text == '') {
								text = decodeURIComponent('%E4%B8%8D%E5%85%81%E8%AE%B8%E8%A7%82%E7%9C%8B%E6%88%96%E8%AF%95%E7%9C%8B%E6%97%B6%E9%97%B4%E7%94%A8%E5%B0%BD');
							}
							this.videoBox.innerHTML = cc_js_Player.createTips(params, text);
						} else {
							var freeT = setInterval(function () {
								if (_this.oVideo.currentTime >= video.freetime) {
									var text = video.authmessage;

									if (text == '') {
										text = decodeURIComponent('%E4%B8%8D%E5%85%81%E8%AE%B8%E8%A7%82%E7%9C%8B%E6%88%96%E8%AF%95%E7%9C%8B%E6%97%B6%E9%97%B4%E7%94%A8%E5%B0%BD');
									}
									_this.videoBox.innerHTML = cc_js_Player.createTips(params, text);

									clearTimeout(freeT);
									if (video.callback != "") {

										if (typeof window[video.callback] == "function") {

											window[video.callback]();
										}
									}
								}
							}, 1000);
						}
					}
				},
				//跑马灯
				//创建跑马灯
				createMarquee: function () {
					//播放事件只触发一次就移除防止创建多个
					window.oPlayer.oVideo.removeEventListener('playing', window.oPlayer.createMarquee)
					//初始化跑马灯元素
					if (video.marquee.type === 'text') {
						window.oPlayer.marqueeBox.innerText = video.marquee.text.content;
						window.oPlayer.marqueeBox.style.fontSize = video.marquee.text.font_size;
						window.oPlayer.marqueeBox.style.color = video.marquee.text.color.replace("0x", "#");
					} else {
						window.oPlayer.marqueeBox.innerHTML = "<img src=" + video.marquee.image.image_url + " width=" + video.marquee.image.width + "px" + " height=" + video.marquee.image.height + "px" + " >"
					}
					window.oPlayer.marqueeBox.style.position = 'absolute';
					window.oPlayer.marqueeBox.style.display = 'none';
					window.oPlayer.marqueeBox.style.zIndex = '99'
					window.oPlayer.marqueeBox.style.whiteSpace = 'nowrap';
					// window.oPlayer.marqueeBox.id = 'ccMarqueeBox'
					//初始化完毕，上树
					window.oPlayer.oDiv.children[0].appendChild(window.oPlayer.marqueeBox);
					//添加过度结束事件
					window.oPlayer.marqueeBox.addEventListener('transitionend', window.oPlayer.animationEnd);
					//动画开始
					window.oPlayer.marqueeAnimation(video.marquee.action)
				},
				marqueeAnimation: function (action) {
					window.oPlayer.marqueeBox.style.transition = "unset"
					window.oPlayer.marqueeBox.style.display = 'block'
					//start fram
					window.oPlayer.marqueeBox.style.left = action[window.oPlayer.currentActionIdx].start.xpos * window.oPlayer.oVideo.clientWidth + "px";
					window.oPlayer.marqueeBox.style.top = action[window.oPlayer.currentActionIdx].start.ypos * window.oPlayer.oVideo.clientHeight + "px";
					window.oPlayer.marqueeBox.style.opacity = action[window.oPlayer.currentActionIdx].start.alpha;
					//end fram
					//直接改变状态无法触发过度动画，需要异步触发
					setTimeout(function () {
						window.oPlayer.marqueeBox.style.transition = "all " + action[window.oPlayer.currentActionIdx].duration / 1000 + "s" + " linear";
					}, 0);
					setTimeout(function () {
						window.oPlayer.marqueeBox.style.left = action[window.oPlayer.currentActionIdx].end.xpos * window.oPlayer.oVideo.clientWidth + "px";
						window.oPlayer.marqueeBox.style.top = action[window.oPlayer.currentActionIdx].end.ypos * window.oPlayer.oVideo.clientHeight + "px";
						window.oPlayer.marqueeBox.style.opacity = action[window.oPlayer.currentActionIdx].end.alpha;
					}, 100);
				},
				animationEnd: function () {
					//transitionend会触发多次 这样触发一次后移除事件防止多次触发
					window.oPlayer.marqueeBox.removeEventListener('transitionend', window.oPlayer.animationEnd);
					window.oPlayer.marqueeBox.style.display = 'none'
					window.oPlayer.currentActionIdx++;
					//action为最后一项时说明当前循环结束
					if (window.oPlayer.currentActionIdx === video.marquee.action.length) {
						//循环减一
						//递归停止的条件 
						if (--window.oPlayer.marqueeLoop === 0) {
							return;
						}
						//重置currentaction
						window.oPlayer.currentActionIdx = 0;
						//去除transition
					}
					window.oPlayer.marqueeAnimation(video.marquee.action)
					//由于transitionend事件改变多少个属性就会导致触发多少次end事件，因此同步直接添加回end事件依然会出现触发多次的现象，延迟执行等end完全结束后重新添加

					setTimeout(function () {
						window.oPlayer.marqueeBox.addEventListener('transitionend', window.oPlayer.animationEnd);
					}, 10);

				},
				play2: function () {
					this.playBtn2.style.display = 'none';
					this.videoTime2();
				},
				pause: function () {
					this.oVideo.pause();
					//this.playBtn.show();
					this.toggleBtn.className = "ccH5TogglePlay";
				},
				endFullscreen: function () {
					if (this.oVideo.clientHeight != screen.height && this.oVideo.src != cc_js_Player.getDefaultCopy(video).playurl.replace(/http:/i, "https:") && cc_js_Player.isIPhone()) {
						this.playBtn2.style.display = 'block';
						clearInterval(timer);
					}
				},
				togglePlay: function () {
					if (this.oVideo.paused) {
						this.play();
					} else {
						this.pause();
					}
				},
				playing: function () {
					this.playBtn.style.display = 'none';
					this.poster.style.display = 'none';
					this.toggleBtn.className = "ccH5TogglePause";
				},
				ended: function () {
					if (cc_js_Player.videoAd == 1 && cc_js_Player.adPlayEnded == true) {
						var adarr = cc_js_Player.materialUrl;
						for (var i = 0; i < adarr.length; i++) {
							if (adarr[i].material == this.oVideo.src) {
								if (i == (adarr.length - 1)) {
									this.oVideo.src = adarr[0].material;
									this.adSrc.href = "https://imedia.bokecc.com/servlet/mobile/clickstats?adid="
										+ cc_js_Player.adId + "&clickurl=" + encodeURIComponent(adarr[0].clickurl) + "&materialid=" + adarr[0].materialid;
									this.vadSrc.href = "https://imedia.bokecc.com/servlet/mobile/clickstats?adid="
										+ cc_js_Player.adId + "&clickurl=" + encodeURIComponent(adarr[0].clickurl) + "&materialid=" + adarr[0].materialid;
								} else {
									this.oVideo.src = adarr[i + 1].material;
									this.adSrc.href = "https://imedia.bokecc.com/servlet/mobile/clickstats?adid="
										+ cc_js_Player.adId + "&clickurl=" + encodeURIComponent(adarr[i + 1].clickurl) + "&materialid=" + adarr[i + 1].materialid;
									this.vadSrc.href = "https://imedia.bokecc.com/servlet/mobile/clickstats?adid="
										+ cc_js_Player.adId + "&clickurl=" + encodeURIComponent(adarr[i + 1].clickurl) + "&materialid=" + adarr[i + 1].materialid;
								}
								break;
							}

						}
						this.playBtn.className = "";
						this.oVideo.play();
					} else {
						this.exitFullsBtn.click();
						this.toggleBtn.className = "ccH5TogglePlay";
						this.playBtn.className = "adrPlayBtn";
						this.playBtn.style.display = 'block';
					}
				},
				skipAd: function () {
					clearInterval(timer);
					this.oVideo.src = cc_js_Player.skipSrc(params, video);
					this.oLoading.style.display = 'block';
					var _this = this;
					this.oVideo.onloadedmetadata = function () {
						_this.oLoading.style.display = 'none';
						_this.oVideo.play();
						_this.toggleFade();
					}
					this.playBtn2.style.display = 'none';
					this.advert.style.display = 'none';
					this.picAd.style.display = 'none';
					cc_js_Player.adPlayEnded = false;
					cc_js_Player.isAdTime = false;
					if (video.vrmode == 1) {
						window.on_vr_init();
					}
					if (params.mediatype == 2) {
						this.audioBg.style.display = 'block';
					}
					if (video.isopenbarrage == 1 && !(ts.isAndroid() || ts.isIPhone() || ts.isIPad())) {
						ts.loadScript("https://p.bokecc.com/js/player/ccBarrage.js");
					}
					//on_cc_h5player_init
					if (typeof window.on_cc_h5player_init === 'function') {
						window.on_cc_h5player_init();
					}
				},
				seeking: function () {
					this.oLoading.style.display = 'block';
				},
				seeked: function () {
					this.oLoading.style.display = 'none';
				},
				waiting: function () {
					this.oLoading.style.display = 'block';
				},
				canplay: function () {
					this.oLoading.style.display = 'none';
					if (!this.startPlayed && !(ts.isIPad() || ts.isIPhone() || ts.isAndroid())) {
						this.startPlayed = true;
						this.hightlightsInit();
					}
				},
				hightlightsInit: function () {
					if (!!video.videomarks) {
						for (var i = 0; i < video.videomarks.length; i++) {
							var oTime = video.videomarks[i].marktime;
							var oText = video.videomarks[i].markdesc;
							this.createHightlights(oTime, oText);
							var oPos = parseInt(oTime / this.oVideo.duration * this.ctrlWidth);
							if (oPos < 60) {
								document.getElementsByClassName('ccHightlightsTips')[i].classList.add('hov');
							}
							if (oPos > (this.ctrlWidth - 60)) {
								document.getElementsByClassName('ccHightlightsTips')[i].classList.add('lasthov');
							}
						}
					}
				},
				createHightlights: function (time, text) {
					var oSpan = document.createElement('span');
					var videoTime = parseInt(this.oVideo.duration);
					oSpan.className = 'ccHightlights';
					oSpan.style.left = time / videoTime * 100 + '%';
					oSpan.innerHTML = "<span class='ccHightlightsTips'>" + text + "</span>";
					this.progressBar.appendChild(oSpan);
				},
				ctrlFadeIn: function () {
					clearTimeout(window.hide_timer);
					var _this = this;
					this.ctrlBar.className = "ccH5FadeIn";
					//audio this.moreBtn
					this.moreBtn.className = "ccH5More ccH5FadeIn";
					window.hide_timer = setTimeout(function () {
						_this.ctrlBar.className = "ccH5FadeOut";
						//audio
						_this.moreBtn.className = "ccH5More ccH5FadeOut";
						_this.hideTheBtnList();
					}, 4000);
				},
				ctrlFadeOut: function () {
					clearTimeout(window.hide_timer);
					this.ctrlBar.className = "ccH5FadeOut";
					//audio
					this.moreBtn.className = "ccH5More ccH5FadeOut";
					this.btnList.style.display = "none";
					this.showBtnList = false;
				},
				toggleFade: function () {
					var _this = this;
					this.addListener(this.videoBox, 'mouseover', function () { _this.ctrlFadeIn(); });
					this.addListener(this.videoBox, 'mousemove', function () { _this.ctrlFadeIn(); });
					// 使用mouseout会导致离开更多按钮触发，导致切换按钮消失
					this.addListener(this.videoBox, 'mouseleave', function () { _this.ctrlFadeOut(); });
				},
				toggleCtrlBar: function () {
					if (this.ctrlBar.className == "ccH5FadeOut") {
						this.ctrlFadeIn();
					} else {
						this.ctrlFadeOut();
					}
				},
				timeFormat: function (time) {
					var t = parseInt(time),
						h, i, s;
					h = Math.floor(t / 3600);
					h = h ? (h + ':') : '';
					i = h ? Math.floor(t % 3600 / 60) : Math.floor(t / 60);
					s = Math.floor(t % 60);
					i = i > 9 ? i : '0' + i;
					s = s > 9 ? s : '0' + s;
					return (h + i + ':' + s);
				},
				//更新进度条
				timeupdate: function () {
					//缓存当前视频的播放时间
					if (this.oVideo.currentTime > 10) {
						localStorage.setItem(params.vid, this.oVideo.currentTime);
					}
					if (this.oVideo.currentTime > 0.1) {
						this.timeTotal.innerHTML = this.timeFormat(this.oVideo.duration);
					}
					this.timeCurrent.innerHTML = this.timeFormat(this.oVideo.currentTime);
					this.proCurrent.style.width = this.oVideo.currentTime / this.oVideo.duration * this.ctrlWidth + 'px';
				},
				progress: function () {

					if (this.oVideo.buffered && this.oVideo.buffered.length > 0) {
						var progressWidth = Math.round(this.oVideo.buffered.end(0) / this.oVideo.duration * this.ctrlWidth);
						this.loadBar.style.width = progressWidth + "px";
						//中意人寿皮肤定制
						if (video.uid == "240837") {
							this.loadBar.style.width = progressWidth + 12 + "px";
						}
					}
				},
				setDura: function (v) {
					this.oVideo.currentTime = v;
				},
				getPos: function (obj) {
					if (obj.getBoundingClientRect) {
						var pos = obj.getBoundingClientRect();
						return {
							left: pos.left,
							top: pos.top
						};
					}
					var x = e.offsetLeft, y = e.offsetTop;
					while (e = e.offsetParent) {
						x += e.offsetLeft;
						y += e.offsetTop;
					}
					return {
						left: x,
						top: y
					};
				},
				getMousePos: function (ev) {
					//修复bug SupportsTouches失效,改变判断依据
					// var SupportsTouches = ("createTouch" in document);
					var SupportsTouches = ("ontouchstart" in window);
					var x = y = 0,
						doc = document.documentElement,
						body = document.body;
					if (!ev) ev = window.event;
					if (window.pageYoffset) {
						x = window.pageXOffset;
						y = window.pageYOffset;
					} else {
						x = (doc && doc.scrollLeft || body && body.scrollLeft || 0) - (doc && doc.clientLeft || body && body.clientLeft || 0);
						y = (doc && doc.scrollTop || body && body.scrollTop || 0) - (doc && doc.clientTop || body && body.clientTop || 0);
					}
					if (SupportsTouches && (ts.isIPad() || ts.isAndroid() || ts.isIPhone())) {
						var evt = ev.touches.item(0);
						x = evt.pageX;
						y = evt.pageY;
					} else {
						x += ev.clientX;
						y += ev.clientY;
					}
					return { 'x': x, 'y': y };
				},
				drag: function (el) {
					var _this = this;
					el.ontouchstart = el.onmousedown = function (e) {
						e.preventDefault();
						document.ontouchmove = document.onmousemove = function (ev) {
							ev.preventDefault();
							_this.pause();
							_this.playBtn.style.display = 'none';
							_this.posDuration(ev);
							_this.oInfo.style.display = 'block';
							if (_this.oVideo.currentTime < 0) {
								_this.oInfo.innerHTML = _this.timeFormat(0);
							} else if (_this.oVideo.currentTime > _this.oVideo.duration) {
								_this.oInfo.innerHTML = _this.timeFormat(_this.oVideo.duration - 1);
								_this.oVideo.currentTime = _this.oVideo.duration - 1;
							} else {
								_this.oInfo.innerHTML = _this.timeFormat(_this.oVideo.currentTime);
							}
						};
						document.ontouchend = document.onmouseup = function () {
							document.ontouchend = document.onmouseup = document.ontouchmove = document.onmousemove = null;
							_this.oInfo.innerHTML = '';
							_this.oInfo.style.display = 'none';
							setTimeout(function () {
								_this.play();
							}, 100);
						};
					};
				},
				dragVm: function (el) {
					var _this = this;
					el.ontouchstart = el.onmousedown = function (e) {
						e.preventDefault();
						document.ontouchmove = document.onmousemove = function (ev) {
							ev.preventDefault();
							_this.posVm(ev);
						};
						document.ontouchend = document.onmouseup = function () {
							document.ontouchend = document.onmouseup = document.ontouchmove = document.onmousemove = null;

						};
					};
				},
				infoFadeIn: function () {
					var _this = this;
					this.oInfo.style.display = 'block';
					clearInterval(timer);
					var timer = setTimeout(function () {
						_this.oInfo.style.display = 'none';
					}, 200);
				},
				pre: function () {
					this.oInfo.innerHTML = "<span class='ccH5playerPre'>L</span>5S";
				},
				next: function () {
					this.oInfo.innerHTML = "<span class='ccH5playerNext'>R</span>5S";
				},
				singleTouch: function (e) {
					var _this = this;
					e.ontouchstart = function (e) {
						e.preventDefault();
						if (e.changedTouches.length == 1) {
							var startX = e.changedTouches[0].pageX;
							document.ontouchend = function () {
								document.ontouchend = document.ontouchmove = null;
								if (e.touches.length == 1) {
									var endX = e.changedTouches[0].pageX;
									var offset = endX - startX;
									if (_this.oVideo.currentTime != _this.oVideo.duration) {
										if (offset < -50) {
											_this.ctrlFadeIn();
											_this.pre();
											_this.infoFadeIn();
											_this.setDura(_this.oVideo.currentTime - 5);
										} else if (offset > 50) {
											_this.ctrlFadeIn();
											_this.next();
											_this.infoFadeIn();
											_this.setDura(_this.oVideo.currentTime + 5);
										}
									}
								}
							};
						}
					};
				},
				posDuration: function (e) {
					var mousePos = this.getMousePos(e),
						probarPos = this.getPos(this.progressBar),
						proCurrent = mousePos.x - probarPos.left,
						time = proCurrent / this.ctrlWidth * this.oVideo.duration;
					this.setDura(time);
				},
				mute: function () {
					this.videoMute = !this.videoMute;
					if (this.videoMute && this.oVideo.volume != 0) {
						this.vmPro.style.top = 80 + 'px';
						this.vmPro.style.height = 0 + 'px';
						this.oVideo.volume = 0;
						this.vmBtn.style.backgroundPosition = 'right center';
					} else {
						this.vmPro.style.top = 20 + 'px';
						this.vmPro.style.height = 60 + 'px';
						this.oVideo.volume = 0.8;
						this.vmBtn.style.backgroundPosition = 'left center';
					}
				},
				setVol: function (v) {
					this.oVideo.volume = v;
				},
				posVm: function (e) {
					var mousePos = this.getMousePos(e).y - window.pageYOffset,
						vmbarPos = this.getPos(this.vmBar).top;
					var h = mousePos - vmbarPos;
					if (h < 80 && h > 0) {
						var t = this.vmPro.style.top = h + 'px';
						var vh = this.vmPro.style.height = 80 - h + 'px';
						vol = ((80 - h) / 80).toFixed(1);
						this.setVol(vol);
						if (vol == 0) {
							this.vmBtn.style.backgroundPosition = 'right center';
							this.videoMute = true;
						} else {
							this.vmBtn.style.backgroundPosition = 'left center';
							this.videoMute = false;
						}
					}
				},
				fullScreen: function (el) {
					if (ts.isAndroid() || ts.isIPhone() || ts.isIPad() || ts.isWindowsPhoneOS()) {

						if (this.oVideo.requestFullscreen) {
							this.oVideo.requestFullscreen();
						} else if (this.oVideo.msRequestFullscreen) {
							this.oVideo.msRequestFullscreen();
						} else if (this.oVideo.mozRequestFullScreen) {
							this.oVideo.mozRequestFullScreen();
						} else if (this.oVideo.webkitSupportsFullscreen) {
							this.oVideo.webkitEnterFullscreen();
						}
						this.autoProgressBar();
					} else {
						var rfs = el.requestFullScreen || el.webkitRequestFullScreen || el.mozRequestFullScreen || el.msRequestFullScreen;
						if (typeof rfs != "undefined" && rfs) {
							rfs.call(el);
						}
						this.fullsBtn.style.display = "none";
						this.exitFullsBtn.style.display = "block";
						this.videoBox.classList.add('ccH5PlayerBoxFixed');
						this.ctrlWidth = this.videoBox.clientWidth;
						this.progress();
						this.timeupdate();
					}
					ts.fixFirefoxFullscreenBug();
				},
				exitFullScreen: function (el) {

					if (ts.isAndroid() || ts.isIPhone() || ts.isIPad() || ts.isWindowsPhoneOS()) {
						if (this.oVideo.exitFullscreen) {

							this.oVideo.exitFullscreen();
						} else if (this.oVideo.msExitFullscreen) {

							this.oVideo.msExitFullscreen();
						} else if (this.oVideo.mozExitFullScreen) {

							this.oVideo.mozExitFullScreen();
						} else if (this.oVideo.webkitExitFullscreen) {

							this.oVideo.webkitExitFullscreen();
						} else if (document.mozCancelFullScreen) {

							document.mozCancelFullScreen();
						}

						this.autoProgressBar();
					} else {
						var el = document,
							cfs = el.cancelFullScreen || el.webkitCancelFullScreen || el.mozCancelFullScreen || el.exitFullScreen;
						if (typeof cfs != "undefined" && cfs) {
							cfs.call(el);
						}
						this.fullsBtn.style.display = "block";
						this.exitFullsBtn.style.display = "none";
						this.videoBox.classList.remove('ccH5PlayerBoxFixed');
						this.ctrlWidth = this.videoBox.clientWidth;
						this.progress();
						this.timeupdate();
					}
				}
			};
			window.oPlayer = new CCHtml5Player();
			oPlayer.init(params.divid);
		},
		getScript: function (url, success) {
			var _this = this;
			var readyState = false,
				script = document.createElement('script');
			script.src = url;

			script.onload = script.onreadystatechange = function () {
				if (!readyState && (!this.readyState || this.readyState == 'loaded' || this.readyState == 'complete')) {
					readyState = true;
					success && success();
					_this.loadJsArr[url] = 1;
				}
			};
			document.body.appendChild(script);
		},
		loadScript: function (res, callback) {
			this.loadJsArr = {};
			if (typeof res === 'string') {
				var _res = res;
				res = [];
				res.push(_res);
			}
			for (var i = 0; i < res.length; i++) {
				this.loadJsArr[res[i]] = 0;
			}
			var _this = this,
				queue = function (fs, cb) {
					_this.getScript(fs.shift(), function () {
						fs.length ? queue(fs, cb) : cb && cb();
					});
				};

			queue(res, callback);
		},
		playerView: function (video, params) {
			if (video.vrmode == 1) {
				this.loadScript([
					"https://p.bokecc.com/js/player/three.min.js",
					"https://p.bokecc.com/js/player/DeviceOrientationControls.js",
					"https://p.bokecc.com/js/player/ccvr.js"
				], function () {
					var isover = true;
					for (var n in this.loadJsArr) {
						if (_this.loadJsArr[n] == 0) {
							isover = false;
						}
					}
					if (isover && cc_js_Player.videoAd != 1) {
						window.on_vr_init();
					}
				});
			}
			if (video.isopenbarrage == 1 && !(this.isAndroid() || this.isIPhone() || this.isIPad()) && this.videoAd != 1) {
				this.loadScript("https://p.bokecc.com/js/player/ccBarrage.js");
			}
			//on_cc_h5player_init
			if (typeof window.on_cc_h5player_init === 'function' && cc_js_Player.videoAd != 1) {
				window.on_cc_h5player_init();
			}
			// if (this.isAndroid() || this.isIPhone() || this.isIPad() || this.isWindowsPhoneOS()) {
			//if (true) {

			// play statistic
			if (!this.isIE()) {
				var vm = document.createElement("script");
	
				vm.classList.add('ccH5VideoScriptTag')
				window.upid = video.UPID;
				vm.src = "https://p.bokecc.com/js/player/statistic.js?v20161219";
				// vm.src = './statistic.js'
				// document.head.appendChild(vm);
				document.getElementsByTagName('head')[0].appendChild(vm);
				// head.insertBefore(vm, head.firstChild);
				vm.onload = function () {
					var videoMonitor = new VideoMonitor({
						uid: params.siteid,
						vid: params.vid,
						video: 'cc_' + params.vid
					});
					videoMonitor.start();
	
					if (typeof window.readyComplete === 'function') {
						window.readyComplete();
					}
				};
			}
			//}

			if (this.isAndroid() || (video.playtype == 1 && !this.isIE()) || this.isIPad() || this.isWindowsPhoneOS() || this.isIPhone()) {
				if (video.status == 1 || this.isRealtimePlay(video)) {
					var ts = this;
					this.html5PlayerSkin(params, video, ts);
				}
			}
			return;
		},
		showPlayerView: function (video) {
			// callback to show video
			// var copies = video.copies;
			// video.copies = copies;
			var video_div = document.getElementById(video.divid);
			for (var i = 0; i < this.videoInfo.length; i++) {
				var params = this.videoInfo[i];
				if (params.divid == video.divid) {
					// show view
					if (!!video.passwd) {
						this.loadScript("https://p.bokecc.com/js/player/cc_md5.js");
						video_div.innerHTML = this.createPassword(params, video);
						this.checkPassword(video, params);
					} else {
						video_div.innerHTML = this.getVideoCode(params, video);
						this.playerView(video, params);
					}
				}
			}
		},
		fixFirefoxFullscreenBug: function () {
			//火狐浏览器全屏时没有控制条的bug
			if ((/firefox/i).test(navigator.userAgent)) {
				function changeControls() {
					document.removeEventListener('mozfullscreenchange', changeControls)
					oPlayer.oVideo.removeAttribute('controls');
				}
				oPlayer.oVideo.setAttribute('controls', 'controls');
				setTimeout(function () { document.addEventListener('mozfullscreenchange', changeControls) }, 50);
			}
		},
		checkVideoNetworkState: function (_this, video, checkStateTimer) {
			checkStateTimer = setInterval(function () {
				//获取不到数据，切换到备用线路
				if (_this.oVideo.networkState === 3) {
					_this.oVideo.src = video.copies[0].backupurl.replace(/http:/i, "https:");
					clearInterval(checkStateTimer);
				} else {
					//浏览器可以获取到数据 关闭定时器
					clearInterval(checkStateTimer);
				}
			}, 1000);
		}

	};
	// Expose to global
	window.cc_js_Player = new Player();
	// End self-executing function
})(window);
