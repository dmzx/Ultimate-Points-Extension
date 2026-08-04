(function (a) {
	if (typeof define === "function" && define.amd) {
		define(["jquery"], a);
	} else {
		a(jQuery);
	}
})(function (a) {
	a.ui = a.ui || {};
	var b = a.ui.version = "1.12.1";
	(function () {
		var b, c = Math.max, d = Math.abs, e = /left|center|right/, f = /top|center|bottom/,
			g = /[\+\-]\d+(\.[\d]+)?%?/, h = /^\w+/, i = /%$/, j = a.fn.pos;

		function k(a, b, c) {
			return [parseFloat(a[0]) * (i.test(a[0]) ? b / 100 : 1), parseFloat(a[1]) * (i.test(a[1]) ? c / 100 : 1)];
		}

		function l(b, c) {
			return parseInt(a.css(b, c), 10) || 0;
		}

		function m(b) {
			var c = b[0];
			if (c.nodeType === 9) {
				return {
					width: b.width(),
					height: b.height(),
					offset: {
						top: 0,
						left: 0
					}
				};
			}
			if (a.isWindow(c)) {
				return {
					width: b.width(),
					height: b.height(),
					offset: {
						top: b.scrollTop(),
						left: b.scrollLeft()
					}
				};
			}
			if (c.preventDefault) {
				return {
					width: 0,
					height: 0,
					offset: {
						top: c.pageY,
						left: c.pageX
					}
				};
			}
			return {
				width: b.outerWidth(),
				height: b.outerHeight(),
				offset: b.offset()
			};
		}

		a.pos = {
			scrollbarWidth: function () {
				if (b !== undefined) {
					return b;
				}
				var c, d,
					e = a("<div " + "style='display:block;position:absolute;width:50px;height:50px;overflow:hidden;'>" + "<div style='height:100px;width:auto;'></div></div>"),
					f = e.children()[0];
				a("body").append(e);
				c = f.offsetWidth;
				e.css("overflow", "scroll");
				d = f.offsetWidth;
				if (c === d) {
					d = e[0].clientWidth;
				}
				e.remove();
				return b = c - d;
			},
			getScrollInfo: function (b) {
				var c = b.isWindow || b.isDocument ? "" : b.element.css("overflow-x"),
					d = b.isWindow || b.isDocument ? "" : b.element.css("overflow-y"),
					e = c === "scroll" || c === "auto" && b.width < b.element[0].scrollWidth,
					f = d === "scroll" || d === "auto" && b.height < b.element[0].scrollHeight;
				return {
					width: f ? a.pos.scrollbarWidth() : 0,
					height: e ? a.pos.scrollbarWidth() : 0
				};
			},
			getWithinInfo: function (b) {
				var c = a(b || window), d = a.isWindow(c[0]), e = !!c[0] && c[0].nodeType === 9, f = !d && !e;
				return {
					element: c,
					isWindow: d,
					isDocument: e,
					offset: f ? a(b).offset() : {
						left: 0,
						top: 0
					},
					scrollLeft: c.scrollLeft(),
					scrollTop: c.scrollTop(),
					width: c.outerWidth(),
					height: c.outerHeight()
				};
			}
		};
		a.fn.pos = function (b) {
			if (!b || !b.of) {
				return j.apply(this, arguments);
			}
			b = a.extend({}, b);
			var i, n, o, p, q, r, s = a(b.of), t = a.pos.getWithinInfo(b.within), u = a.pos.getScrollInfo(t),
				v = (b.collision || "flip").split(" "), w = {};
			r = m(s);
			if (s[0].preventDefault) {
				b.at = "left top";
			}
			n = r.width;
			o = r.height;
			p = r.offset;
			q = a.extend({}, p);
			a.each(["my", "at"], function () {
				var a = (b[this] || "").split(" "), c, d;
				if (a.length === 1) {
					a = e.test(a[0]) ? a.concat(["center"]) : f.test(a[0]) ? ["center"].concat(a) : ["center", "center"];
				}
				a[0] = e.test(a[0]) ? a[0] : "center";
				a[1] = f.test(a[1]) ? a[1] : "center";
				c = g.exec(a[0]);
				d = g.exec(a[1]);
				w[this] = [c ? c[0] : 0, d ? d[0] : 0];
				b[this] = [h.exec(a[0])[0], h.exec(a[1])[0]];
			});
			if (v.length === 1) {
				v[1] = v[0];
			}
			if (b.at[0] === "right") {
				q.left += n;
			} else if (b.at[0] === "center") {
				q.left += n / 2;
			}
			if (b.at[1] === "bottom") {
				q.top += o;
			} else if (b.at[1] === "center") {
				q.top += o / 2;
			}
			i = k(w.at, n, o);
			q.left += i[0];
			q.top += i[1];
			return this.each(function () {
				var e, f, g = a(this), h = g.outerWidth(), j = g.outerHeight(), m = l(this, "marginLeft"),
					r = l(this, "marginTop"), x = h + m + l(this, "marginRight") + u.width,
					y = j + r + l(this, "marginBottom") + u.height, z = a.extend({}, q),
					A = k(w.my, g.outerWidth(), g.outerHeight());
				if (b.my[0] === "right") {
					z.left -= h;
				} else if (b.my[0] === "center") {
					z.left -= h / 2;
				}
				if (b.my[1] === "bottom") {
					z.top -= j;
				} else if (b.my[1] === "center") {
					z.top -= j / 2;
				}
				z.left += A[0];
				z.top += A[1];
				e = {
					marginLeft: m,
					marginTop: r
				};
				a.each(["left", "top"], function (c, d) {
					if (a.ui.pos[v[c]]) {
						a.ui.pos[v[c]][d](z, {
							targetWidth: n,
							targetHeight: o,
							elemWidth: h,
							elemHeight: j,
							collisionPosition: e,
							collisionWidth: x,
							collisionHeight: y,
							offset: [i[0] + A[0], i[1] + A[1]],
							my: b.my,
							at: b.at,
							within: t,
							elem: g
						});
					}
				});
				if (b.using) {
					f = function (a) {
						var e = p.left - z.left, f = e + n - h, i = p.top - z.top, k = i + o - j, l = {
							target: {
								element: s,
								left: p.left,
								top: p.top,
								width: n,
								height: o
							},
							element: {
								element: g,
								left: z.left,
								top: z.top,
								width: h,
								height: j
							},
							horizontal: f < 0 ? "left" : e > 0 ? "right" : "center",
							vertical: k < 0 ? "top" : i > 0 ? "bottom" : "middle"
						};
						if (n < h && d(e + f) < n) {
							l.horizontal = "center";
						}
						if (o < j && d(i + k) < o) {
							l.vertical = "middle";
						}
						if (c(d(e), d(f)) > c(d(i), d(k))) {
							l.important = "horizontal";
						} else {
							l.important = "vertical";
						}
						b.using.call(this, a, l);
					};
				}
				g.offset(a.extend(z, {
					using: f
				}));
			});
		};
		a.ui.pos = {
			_trigger: function (a, b, c, d) {
				if (b.elem) {
					b.elem.trigger({
						type: c,
						position: a,
						positionData: b,
						triggered: d
					});
				}
			},
			fit: {
				left: function (b, d) {
					a.ui.pos._trigger(b, d, "posCollide", "fitLeft");
					var e = d.within, f = e.isWindow ? e.scrollLeft : e.offset.left, g = e.width,
						h = b.left - d.collisionPosition.marginLeft, i = f - h, j = h + d.collisionWidth - g - f, k;
					if (d.collisionWidth > g) {
						if (i > 0 && j <= 0) {
							k = b.left + i + d.collisionWidth - g - f;
							b.left += i - k;
						} else if (j > 0 && i <= 0) {
							b.left = f;
						} else {
							if (i > j) {
								b.left = f + g - d.collisionWidth;
							} else {
								b.left = f;
							}
						}
					} else if (i > 0) {
						b.left += i;
					} else if (j > 0) {
						b.left -= j;
					} else {
						b.left = c(b.left - h, b.left);
					}
					a.ui.pos._trigger(b, d, "posCollided", "fitLeft");
				},
				top: function (b, d) {
					a.ui.pos._trigger(b, d, "posCollide", "fitTop");
					var e = d.within, f = e.isWindow ? e.scrollTop : e.offset.top, g = d.within.height,
						h = b.top - d.collisionPosition.marginTop, i = f - h, j = h + d.collisionHeight - g - f, k;
					if (d.collisionHeight > g) {
						if (i > 0 && j <= 0) {
							k = b.top + i + d.collisionHeight - g - f;
							b.top += i - k;
						} else if (j > 0 && i <= 0) {
							b.top = f;
						} else {
							if (i > j) {
								b.top = f + g - d.collisionHeight;
							} else {
								b.top = f;
							}
						}
					} else if (i > 0) {
						b.top += i;
					} else if (j > 0) {
						b.top -= j;
					} else {
						b.top = c(b.top - h, b.top);
					}
					a.ui.pos._trigger(b, d, "posCollided", "fitTop");
				}
			},
			flip: {
				left: function (b, c) {
					a.ui.pos._trigger(b, c, "posCollide", "flipLeft");
					var e = c.within, f = e.offset.left + e.scrollLeft, g = e.width,
						h = e.isWindow ? e.scrollLeft : e.offset.left, i = b.left - c.collisionPosition.marginLeft,
						j = i - h, k = i + c.collisionWidth - g - h,
						l = c.my[0] === "left" ? -c.elemWidth : c.my[0] === "right" ? c.elemWidth : 0,
						m = c.at[0] === "left" ? c.targetWidth : c.at[0] === "right" ? -c.targetWidth : 0,
						n = -2 * c.offset[0], o, p;
					if (j < 0) {
						o = b.left + l + m + n + c.collisionWidth - g - f;
						if (o < 0 || o < d(j)) {
							b.left += l + m + n;
						}
					} else if (k > 0) {
						p = b.left - c.collisionPosition.marginLeft + l + m + n - h;
						if (p > 0 || d(p) < k) {
							b.left += l + m + n;
						}
					}
					a.ui.pos._trigger(b, c, "posCollided", "flipLeft");
				},
				top: function (b, c) {
					a.ui.pos._trigger(b, c, "posCollide", "flipTop");
					var e = c.within, f = e.offset.top + e.scrollTop, g = e.height,
						h = e.isWindow ? e.scrollTop : e.offset.top, i = b.top - c.collisionPosition.marginTop,
						j = i - h, k = i + c.collisionHeight - g - h, l = c.my[1] === "top",
						m = l ? -c.elemHeight : c.my[1] === "bottom" ? c.elemHeight : 0,
						n = c.at[1] === "top" ? c.targetHeight : c.at[1] === "bottom" ? -c.targetHeight : 0,
						o = -2 * c.offset[1], p, q;
					if (j < 0) {
						q = b.top + m + n + o + c.collisionHeight - g - f;
						if (q < 0 || q < d(j)) {
							b.top += m + n + o;
						}
					} else if (k > 0) {
						p = b.top - c.collisionPosition.marginTop + m + n + o - h;
						if (p > 0 || d(p) < k) {
							b.top += m + n + o;
						}
					}
					a.ui.pos._trigger(b, c, "posCollided", "flipTop");
				}
			},
			flipfit: {
				left: function () {
					a.ui.pos.flip.left.apply(this, arguments);
					a.ui.pos.fit.left.apply(this, arguments);
				},
				top: function () {
					a.ui.pos.flip.top.apply(this, arguments);
					a.ui.pos.fit.top.apply(this, arguments);
				}
			}
		};
		(function () {
			var b, c, d, e, f, g = document.getElementsByTagName("body")[0], h = document.createElement("div");
			b = document.createElement(g ? "div" : "body");
			d = {
				visibility: "hidden",
				width: 0,
				height: 0,
				border: 0,
				margin: 0,
				background: "none"
			};
			if (g) {
				a.extend(d, {
					position: "absolute",
					left: "-1000px",
					top: "-1000px"
				});
			}
			for (f in d) {
				b.style[f] = d[f];
			}
			b.appendChild(h);
			c = g || document.documentElement;
			c.insertBefore(b, c.firstChild);
			h.style.cssText = "position: absolute; left: 10.7432222px;";
			e = a(h).offset().left;
			a.support.offsetFractions = e > 10 && e < 11;
			b.innerHTML = "";
			c.removeChild(b);
		})();
	})();
	var c = a.ui.position;
});

(function (a) {
	"use strict";
	if (typeof define === "function" && define.amd) {
		define(["jquery"], a);
	} else if (window.jQuery && !window.jQuery.fn.iconpicker) {
		a(window.jQuery);
	}
})(function (a) {
	"use strict";
	var b = {
		isEmpty: function (a) {
			return a === false || a === "" || a === null || a === undefined;
		},
		isEmptyObject: function (a) {
			return this.isEmpty(a) === true || a.length === 0;
		},
		isElement: function (b) {
			return a(b).length > 0;
		},
		isString: function (a) {
			return typeof a === "string" || a instanceof String;
		},
		isArray: function (b) {
			return a.isArray(b);
		},
		inArray: function (b, c) {
			return a.inArray(b, c) !== -1;
		},
		throwError: function (a) {
			throw "Font Awesome Icon Picker Exception: " + a;
		}
	};
	var c = function (d, e) {
		this._id = c._idCounter++;
		this.element = a(d).addClass("iconpicker-element");
		this._trigger("iconpickerCreate");
		this.options = a.extend({}, c.defaultOptions, this.element.data(), e);
		this.options.templates = a.extend({}, c.defaultOptions.templates, this.options.templates);
		this.options.originalPlacement = this.options.placement;
		this.container = b.isElement(this.options.container) ? a(this.options.container) : false;
		if (this.container === false) {
			if (this.element.is(".dropdown-toggle")) {
				this.container = a("~ .dropdown-menu:first", this.element);
			} else {
				this.container = this.element.is("input,textarea,button,.btn") ? this.element.parent() : this.element;
			}
		}
		this.container.addClass("iconpicker-container");
		if (this.isDropdownMenu()) {
			this.options.templates.search = false;
			this.options.templates.buttons = false;
			this.options.placement = "inline";
		}
		this.input = this.element.is("input,textarea") ? this.element.addClass("iconpicker-input") : false;
		if (this.input === false) {
			this.input = this.container.find(this.options.input);
			if (!this.input.is("input,textarea")) {
				this.input = false;
			}
		}
		this.component = this.isDropdownMenu() ? this.container.parent().find(this.options.component) : this.container.find(this.options.component);
		if (this.component.length === 0) {
			this.component = false;
		} else {
			this.component.find("i").addClass("iconpicker-component");
		}
		this._createPopover();
		this._createIconpicker();
		if (this.getAcceptButton().length === 0) {
			this.options.mustAccept = false;
		}
		if (this.isInputGroup()) {
			this.container.parent().append(this.popover);
		} else {
			this.container.append(this.popover);
		}
		this._bindElementEvents();
		this._bindWindowEvents();
		this.update(this.options.selected);
		if (this.isInline()) {
			this.show();
		}
		this._trigger("iconpickerCreated");
	};
	c._idCounter = 0;
	c.defaultOptions = {
		title: false,
		selected: false,
		defaultValue: false,
		placement: "bottom",
		collision: "none",
		animation: true,
		hideOnSelect: false,
		showFooter: false,
		searchInFooter: false,
		mustAccept: false,
		selectedCustomClass: "bg-primary",
		icons: [],
		fullClassFormatter: function (a) {
			return "fa " + a;
		},
		input: "input,.iconpicker-input",
		inputSearch: false,
		container: false,
		component: ".input-group-addon,.iconpicker-component",
		templates: {
			popover: '<div class="iconpicker-popover popover"><div class="arrow"></div>' + '<div class="popover-title"></div><div class="popover-content"></div></div>',
			footer: '<div class="popover-footer"></div>',
			buttons: '<button class="iconpicker-btn iconpicker-btn-cancel btn btn-default btn-sm">Cancel</button>' + ' <button class="iconpicker-btn iconpicker-btn-accept btn btn-primary btn-sm">Accept</button>',
			search: '<input type="search" class="form-control iconpicker-search" placeholder="Type to filter" />',
			iconpicker: '<div class="iconpicker"><div class="iconpicker-items"></div></div>',
			iconpickerItem: '<a role="button" href="#" class="iconpicker-item"><i></i></a>'
		}
	};
	c.batch = function (b, c) {
		var d = Array.prototype.slice.call(arguments, 2);
		return a(b).each(function () {
			var b = a(this).data("iconpicker");
			if (!!b) {
				b[c].apply(b, d);
			}
		});
	};
	c.prototype = {
		constructor: c,
		options: {},
		_id: 0,
		_trigger: function (b, c) {
			c = c || {};
			this.element.trigger(a.extend({
				type: b,
				iconpickerInstance: this
			}, c));
		},
		_createPopover: function () {
			this.popover = a(this.options.templates.popover);
			var c = this.popover.find(".popover-title");
			if (!!this.options.title) {
				c.append(a('<div class="popover-title-text">' + this.options.title + "</div>"));
			}
			if (this.hasSeparatedSearchInput() && !this.options.searchInFooter) {
				c.append(this.options.templates.search);
			} else if (!this.options.title) {
				c.remove();
			}
			if (this.options.showFooter && !b.isEmpty(this.options.templates.footer)) {
				var d = a(this.options.templates.footer);
				if (this.hasSeparatedSearchInput() && this.options.searchInFooter) {
					d.append(a(this.options.templates.search));
				}
				if (!b.isEmpty(this.options.templates.buttons)) {
					d.append(a(this.options.templates.buttons));
				}
				this.popover.append(d);
			}
			if (this.options.animation === true) {
				this.popover.addClass("fade");
			}
			return this.popover;
		},
		_createIconpicker: function () {
			var b = this;
			this.iconpicker = a(this.options.templates.iconpicker);
			var c = function (c) {
				var d = a(this);
				if (d.is("i")) {
					d = d.parent();
				}
				b._trigger("iconpickerSelect", {
					iconpickerItem: d,
					iconpickerValue: b.iconpickerValue
				});
				if (b.options.mustAccept === false) {
					b.update(d.data("iconpickerValue"));
					b._trigger("iconpickerSelected", {
						iconpickerItem: this,
						iconpickerValue: b.iconpickerValue
					});
				} else {
					b.update(d.data("iconpickerValue"), true);
				}
				if (b.options.hideOnSelect && b.options.mustAccept === false) {
					b.hide();
				}
				c.preventDefault();
				return false;
			};
			for (var d in this.options.icons) {
				if (typeof this.options.icons[d] === "string") {
					var e = a(this.options.templates.iconpickerItem);
					e.find("i").addClass(this.options.fullClassFormatter(this.options.icons[d]));
					e.data("iconpickerValue", this.options.icons[d]).on("click.iconpicker", c);
					this.iconpicker.find(".iconpicker-items").append(e.attr("title", "." + this.options.icons[d]));
				}
			}
			this.popover.find(".popover-content").append(this.iconpicker);
			return this.iconpicker;
		},
		_isEventInsideIconpicker: function (b) {
			var c = a(b.target);
			if ((!c.hasClass("iconpicker-element") || c.hasClass("iconpicker-element") && !c.is(this.element)) && c.parents(".iconpicker-popover").length === 0) {
				return false;
			}
			return true;
		},
		_bindElementEvents: function () {
			var c = this;
			this.getSearchInput().on("keyup.iconpicker", function () {
				c.filter(a(this).val().toLowerCase());
			});
			this.getAcceptButton().on("click.iconpicker", function () {
				var a = c.iconpicker.find(".iconpicker-selected").get(0);
				c.update(c.iconpickerValue);
				c._trigger("iconpickerSelected", {
					iconpickerItem: a,
					iconpickerValue: c.iconpickerValue
				});
				if (!c.isInline()) {
					c.hide();
				}
			});
			this.getCancelButton().on("click.iconpicker", function () {
				if (!c.isInline()) {
					c.hide();
				}
			});
			this.element.on("focus.iconpicker", function (a) {
				c.show();
				a.stopPropagation();
			});
			if (this.hasComponent()) {
				this.component.on("click.iconpicker", function () {
					c.toggle();
				});
			}
			if (this.hasInput()) {
				this.input.on("keyup.iconpicker", function (d) {
					if (!b.inArray(d.keyCode, [38, 40, 37, 39, 16, 17, 18, 9, 8, 91, 93, 20, 46, 186, 190, 46, 78, 188, 44, 86])) {
						c.update();
					} else {
						c._updateFormGroupStatus(c.getValid(this.value) !== false);
					}
					if (c.options.inputSearch === true) {
						c.filter(a(this).val().toLowerCase());
					}
				});
			}
		},
		_bindWindowEvents: function () {
			var b = a(window.document);
			var c = this;
			var d = ".iconpicker.inst" + this._id;
			a(window).on("resize.iconpicker" + d + " orientationchange.iconpicker" + d, function (a) {
				if (c.popover.hasClass("in")) {
					c.updatePlacement();
				}
			});
			if (!c.isInline()) {
				b.on("mouseup" + d, function (a) {
					if (!c._isEventInsideIconpicker(a) && !c.isInline()) {
						c.hide();
					}
					a.stopPropagation();
					a.preventDefault();
					return false;
				});
			}
			return false;
		},
		_unbindElementEvents: function () {
			this.popover.off(".iconpicker");
			this.element.off(".iconpicker");
			if (this.hasInput()) {
				this.input.off(".iconpicker");
			}
			if (this.hasComponent()) {
				this.component.off(".iconpicker");
			}
			if (this.hasContainer()) {
				this.container.off(".iconpicker");
			}
		},
		_unbindWindowEvents: function () {
			a(window).off(".iconpicker.inst" + this._id);
			a(window.document).off(".iconpicker.inst" + this._id);
		},
		updatePlacement: function (b, c) {
			b = b || this.options.placement;
			this.options.placement = b;
			c = c || this.options.collision;
			c = c === true ? "flip" : c;
			var d = {
				at: "right bottom",
				my: "right top",
				of: this.hasInput() && !this.isInputGroup() ? this.input : this.container,
				collision: c === true ? "flip" : c,
				within: window
			};
			this.popover.removeClass("inline topLeftCorner topLeft top topRight topRightCorner " + "rightTop right rightBottom bottomRight bottomRightCorner " + "bottom bottomLeft bottomLeftCorner leftBottom left leftTop");
			if (typeof b === "object") {
				return this.popover.pos(a.extend({}, d, b));
			}
			switch (b) {
				case "inline": {
					d = false;
				}
					break;

				case "topLeftCorner": {
					d.my = "right bottom";
					d.at = "left top";
				}
					break;

				case "topLeft": {
					d.my = "left bottom";
					d.at = "left top";
				}
					break;

				case "top": {
					d.my = "center bottom";
					d.at = "center top";
				}
					break;

				case "topRight": {
					d.my = "right bottom";
					d.at = "right top";
				}
					break;

				case "topRightCorner": {
					d.my = "left bottom";
					d.at = "right top";
				}
					break;

				case "rightTop": {
					d.my = "left bottom";
					d.at = "right center";
				}
					break;

				case "right": {
					d.my = "left center";
					d.at = "right center";
				}
					break;

				case "rightBottom": {
					d.my = "left top";
					d.at = "right center";
				}
					break;

				case "bottomRightCorner": {
					d.my = "left top";
					d.at = "right bottom";
				}
					break;

				case "bottomRight": {
					d.my = "right top";
					d.at = "right bottom";
				}
					break;

				case "bottom": {
					d.my = "center top";
					d.at = "center bottom";
				}
					break;

				case "bottomLeft": {
					d.my = "left top";
					d.at = "left bottom";
				}
					break;

				case "bottomLeftCorner": {
					d.my = "right top";
					d.at = "left bottom";
				}
					break;

				case "leftBottom": {
					d.my = "right top";
					d.at = "left center";
				}
					break;

				case "left": {
					d.my = "right center";
					d.at = "left center";
				}
					break;

				case "leftTop": {
					d.my = "right bottom";
					d.at = "left center";
				}
					break;

				default: {
					return false;
				}
					break;
			}
			this.popover.css({
				display: this.options.placement === "inline" ? "" : "block"
			});
			if (d !== false) {
				this.popover.pos(d).css("maxWidth", a(window).width() - this.container.offset().left - 5);
			} else {
				this.popover.css({
					top: "auto",
					right: "auto",
					bottom: "auto",
					left: "auto",
					maxWidth: "none"
				});
			}
			this.popover.addClass(this.options.placement);
			return true;
		},
		_updateComponents: function () {
			this.iconpicker.find(".iconpicker-item.iconpicker-selected").removeClass("iconpicker-selected " + this.options.selectedCustomClass);
			if (this.iconpickerValue) {
				this.iconpicker.find("." + this.options.fullClassFormatter(this.iconpickerValue).replace(/ /g, ".")).parent().addClass("iconpicker-selected " + this.options.selectedCustomClass);
			}
			if (this.hasComponent()) {
				var a = this.component.find("i");
				if (a.length > 0) {
					a.attr("class", this.options.fullClassFormatter(this.iconpickerValue));
				} else {
					this.component.html(this.getHtml());
				}
			}
		},
		_updateFormGroupStatus: function (a) {
			if (this.hasInput()) {
				if (a !== false) {
					this.input.parents(".form-group:first").removeClass("has-error");
				} else {
					this.input.parents(".form-group:first").addClass("has-error");
				}
				return true;
			}
			return false;
		},
		getValid: function (c) {
			if (!b.isString(c)) {
				c = "";
			}
			var d = c === "";
			c = a.trim(c);
			if (b.inArray(c, this.options.icons) || d) {
				return c;
			}
			return false;
		},
		setValue: function (a) {
			var b = this.getValid(a);
			if (b !== false) {
				this.iconpickerValue = b;
				this._trigger("iconpickerSetValue", {
					iconpickerValue: b
				});
				return this.iconpickerValue;
			} else {
				this._trigger("iconpickerInvalid", {
					iconpickerValue: a
				});
				return false;
			}
		},
		getHtml: function () {
			return '<i class="' + this.options.fullClassFormatter(this.iconpickerValue) + '"></i>';
		},
		setSourceValue: function (a) {
			a = this.setValue(a);
			if (a !== false && a !== "") {
				if (this.hasInput()) {
					this.input.val(this.iconpickerValue);
				} else {
					this.element.data("iconpickerValue", this.iconpickerValue);
				}
				this._trigger("iconpickerSetSourceValue", {
					iconpickerValue: a
				});
			}
			return a;
		},
		getSourceValue: function (a) {
			a = a || this.options.defaultValue;
			var b = a;
			if (this.hasInput()) {
				b = this.input.val();
			} else {
				b = this.element.data("iconpickerValue");
			}
			if (b === undefined || b === "" || b === null || b === false) {
				b = a;
			}
			return b;
		},
		hasInput: function () {
			return this.input !== false;
		},
		isInputSearch: function () {
			return this.hasInput() && this.options.inputSearch === true;
		},
		isInputGroup: function () {
			return this.container.is(".input-group");
		},
		isDropdownMenu: function () {
			return this.container.is(".dropdown-menu");
		},
		hasSeparatedSearchInput: function () {
			return this.options.templates.search !== false && !this.isInputSearch();
		},
		hasComponent: function () {
			return this.component !== false;
		},
		hasContainer: function () {
			return this.container !== false;
		},
		getAcceptButton: function () {
			return this.popover.find(".iconpicker-btn-accept");
		},
		getCancelButton: function () {
			return this.popover.find(".iconpicker-btn-cancel");
		},
		getSearchInput: function () {
			return this.popover.find(".iconpicker-search");
		},
		filter: function (c) {
			if (b.isEmpty(c)) {
				this.iconpicker.find(".iconpicker-item").show();
				return a(false);
			} else {
				var d = [];
				this.iconpicker.find(".iconpicker-item").each(function () {
					var b = a(this);
					var e = b.attr("title").toLowerCase();
					var f = false;
					try {
						f = new RegExp(c, "g");
					} catch (a) {
						f = false;
					}
					if (f !== false && e.match(f)) {
						d.push(b);
						b.show();
					} else {
						b.hide();
					}
				});
				return d;
			}
		},
		show: function () {
			if (this.popover.hasClass("in")) {
				return false;
			}
			a.iconpicker.batch(a(".iconpicker-popover.in:not(.inline)").not(this.popover), "hide");
			this._trigger("iconpickerShow");
			this.updatePlacement();
			this.popover.addClass("in");
			setTimeout(a.proxy(function () {
				this.popover.css("display", this.isInline() ? "" : "block");
				this._trigger("iconpickerShown");
			}, this), this.options.animation ? 300 : 1);
		},
		hide: function () {
			if (!this.popover.hasClass("in")) {
				return false;
			}
			this._trigger("iconpickerHide");
			this.popover.removeClass("in");
			setTimeout(a.proxy(function () {
				this.popover.css("display", "none");
				this.getSearchInput().val("");
				this.filter("");
				this._trigger("iconpickerHidden");
			}, this), this.options.animation ? 300 : 1);
		},
		toggle: function () {
			if (this.popover.is(":visible")) {
				this.hide();
			} else {
				this.show(true);
			}
		},
		update: function (a, b) {
			a = a ? a : this.getSourceValue(this.iconpickerValue);
			this._trigger("iconpickerUpdate");
			if (b === true) {
				a = this.setValue(a);
			} else {
				a = this.setSourceValue(a);
				this._updateFormGroupStatus(a !== false);
			}
			if (a !== false) {
				this._updateComponents();
			}
			this._trigger("iconpickerUpdated");
			return a;
		},
		destroy: function () {
			this._trigger("iconpickerDestroy");
			this.element.removeData("iconpicker").removeData("iconpickerValue").removeClass("iconpicker-element");
			this._unbindElementEvents();
			this._unbindWindowEvents();
			a(this.popover).remove();
			this._trigger("iconpickerDestroyed");
		},
		disable: function () {
			if (this.hasInput()) {
				this.input.prop("disabled", true);
				return true;
			}
			return false;
		},
		enable: function () {
			if (this.hasInput()) {
				this.input.prop("disabled", false);
				return true;
			}
			return false;
		},
		isDisabled: function () {
			if (this.hasInput()) {
				return this.input.prop("disabled") === true;
			}
			return false;
		},
		isInline: function () {
			return this.options.placement === "inline" || this.popover.hasClass("inline");
		}
	};
	a.iconpicker = c;
	a.fn.iconpicker = function (b) {
		return this.each(function () {
			var d = a(this);
			if (!d.data("iconpicker")) {
				d.data("iconpicker", new c(this, typeof b === "object" ? b : {}));
			}
		});
	};
	c.defaultOptions.icons = ["fa-address-book", "fa-address-card", "fa-align-center", "fa-align-justify", "fa-align-left", "fa-align-right", "fa-anchor", "fa-angle-down", "fa-angle-left", "fa-angle-right", "fa-angle-up", "fa-angles-down", "fa-angles-left", "fa-angles-right", "fa-angles-up", "fa-arrow-down", "fa-arrow-down-1-9", "fa-arrow-down-9-1", "fa-arrow-down-a-z", "fa-arrow-down-long", "fa-arrow-down-short-wide", "fa-arrow-down-wide-short", "fa-arrow-down-z-a", "fa-arrow-left", "fa-arrow-left-long", "fa-arrow-pointer", "fa-arrow-right", "fa-arrow-right-arrow-left", "fa-arrow-right-from-bracket", "fa-arrow-right-long", "fa-arrow-right-to-bracket", "fa-arrow-rotate-left", "fa-arrow-turn-down", "fa-arrow-turn-up", "fa-arrow-up", "fa-arrow-up-long", "fa-arrow-up-right-from-square", "fa-arrows-left-right", "fa-arrows-rotate", "fa-arrows-up-down", "fa-arrows-up-down-left-right", "fa-asterisk", "fa-at", "fa-audio-description", "fa-backward", "fa-backward-fast", "fa-backward-step", "fa-bag-shopping", "fa-ban", "fa-barcode", "fa-bars", "fa-bars-staggered", "fa-basket-shopping", "fa-bath", "fa-battery-empty", "fa-battery-full", "fa-battery-half", "fa-battery-quarter", "fa-battery-three-quarters", "fa-bed", "fa-beer-mug-empty", "fa-bell", "fa-bell-slash", "fa-bicycle", "fa-binoculars", "fa-bold", "fa-bolt", "fa-bomb", "fa-book", "fa-bookmark", "fa-box-archive", "fa-braille", "fa-briefcase", "fa-bug", "fa-building", "fa-building-columns", "fa-bullhorn", "fa-bullseye", "fa-bus", "fa-cake-candles", "fa-calculator", "fa-calendar", "fa-calendar-xmark", "fa-camera", "fa-camera-retro", "fa-car", "fa-caret-down", "fa-caret-left", "fa-caret-right", "fa-caret-up", "fa-cart-arrow-down", "fa-cart-plus", "fa-cart-shopping", "fa-certificate", "fa-chart-area", "fa-chart-bar", "fa-chart-column", "fa-chart-line", "fa-chart-pie", "fa-check", "fa-chevron-down", "fa-chevron-left", "fa-chevron-right", "fa-chevron-up", "fa-child", "fa-circle", "fa-circle-arrow-down", "fa-circle-arrow-left", "fa-circle-arrow-right", "fa-circle-arrow-up", "fa-circle-check", "fa-circle-chevron-down", "fa-circle-chevron-left", "fa-circle-chevron-right", "fa-circle-chevron-up", "fa-circle-dot", "fa-circle-down", "fa-circle-exclamation", "fa-circle-half-stroke", "fa-circle-info", "fa-circle-left", "fa-circle-minus", "fa-circle-notch", "fa-circle-pause", "fa-circle-play", "fa-circle-plus", "fa-circle-question", "fa-circle-right", "fa-circle-stop", "fa-circle-up", "fa-circle-user", "fa-circle-xmark", "fa-clipboard", "fa-clock-rotate-left", "fa-clone", "fa-closed-captioning", "fa-cloud", "fa-cloud-arrow-down", "fa-cloud-arrow-up", "fa-code", "fa-code-fork", "fa-comment", "fa-comment-dots", "fa-comments", "fa-compass", "fa-compress", "fa-copy", "fa-copyright", "fa-credit-card", "fa-crop", "fa-crosshairs", "fa-cube", "fa-cubes", "fa-database", "fa-desktop", "fa-diamond", "fa-dollar-sign", "fa-download", "fa-droplet", "fa-ear-deaf", "fa-ear-listen", "fa-eject", "fa-ellipsis", "fa-ellipsis-vertical", "fa-envelope", "fa-envelope-open", "fa-eraser", "fa-euro-sign", "fa-exclamation", "fa-expand", "fa-eye", "fa-eye-dropper", "fa-eye-low-vision", "fa-eye-slash", "fa-face-frown", "fa-face-meh", "fa-face-smile", "fa-fax", "fa-file", "fa-file-audio", "fa-file-image", "fa-file-lines", "fa-file-video", "fa-file-zipper", "fa-film", "fa-filter", "fa-fire", "fa-fire-extinguisher", "fa-flag", "fa-flag-checkered", "fa-flask", "fa-floppy-disk", "fa-folder", "fa-folder-open", "fa-font", "fa-font-awesome", "fa-forward", "fa-forward-fast", "fa-forward-step", "fa-futbol", "fa-gamepad", "fa-gauge", "fa-gauge-simple-high", "fa-gavel", "fa-gear", "fa-gears", "fa-genderless", "fa-gift", "fa-globe", "fa-graduation-cap", "fa-hand", "fa-hand-back-fist", "fa-hand-point-down", "fa-hand-point-left", "fa-hand-point-right", "fa-hand-point-up", "fa-hands", "fa-hands-asl-interpreting", "fa-hard-drive", "fa-hashtag", "fa-heading", "fa-headphones", "fa-heart", "fa-heart-pulse", "fa-hotel", "fa-hourglass", "fa-hourglass-end", "fa-hourglass-half", "fa-hourglass-start", "fa-house", "fa-i-cursor", "fa-id-badge", "fa-id-card", "fa-image", "fa-inbox", "fa-indent", "fa-indian-rupee-sign", "fa-industry", "fa-info", "fa-italic", "fa-jet-fighter", "fa-key", "fa-language", "fa-laptop", "fa-leaf", "fa-life-ring", "fa-link", "fa-link-slash", "fa-list", "fa-list-check", "fa-list-ol", "fa-list-ul", "fa-location-arrow", "fa-location-pin", "fa-lock", "fa-magnet", "fa-magnifying-glass", "fa-magnifying-glass-minus", "fa-magnifying-glass-plus", "fa-map", "fa-map-pin", "fa-mars", "fa-mars-and-venus", "fa-mars-double", "fa-mars-stroke", "fa-mars-stroke-right", "fa-mars-stroke-up", "fa-martini-glass-empty", "fa-mercury", "fa-microchip", "fa-microphone", "fa-microphone-slash", "fa-minus", "fa-mobile", "fa-money-bill-1", "fa-motorcycle", "fa-mug-saucer", "fa-music", "fa-neuter", "fa-note-sticky", "fa-object-group", "fa-object-ungroup", "fa-outdent", "fa-paintbrush", "fa-paper-plane", "fa-paperclip", "fa-paragraph", "fa-paste", "fa-pause", "fa-paw", "fa-pen-to-square", "fa-pencil", "fa-percent", "fa-person", "fa-person-dress", "fa-person-walking-with-cane", "fa-phone", "fa-phone-volume", "fa-plane", "fa-play", "fa-plug", "fa-plus", "fa-podcast", "fa-power-off", "fa-print", "fa-puzzle-piece", "fa-qrcode", "fa-question", "fa-quote-left", "fa-quote-right", "fa-rectangle-list", "fa-rectangle-xmark", "fa-recycle", "fa-registered", "fa-repeat", "fa-reply", "fa-reply-all", "fa-retweet", "fa-road", "fa-rocket", "fa-rotate-left", "fa-rotate-right", "fa-rss", "fa-ruble-sign", "fa-rupee-sign", "fa-scale-balanced", "fa-scissors", "fa-server", "fa-share", "fa-share-from-square", "fa-share-nodes", "fa-shekel-sign", "fa-shield", "fa-ship", "fa-shower", "fa-shuffle", "fa-shuttle-space", "fa-signal", "fa-signs-post", "fa-sitemap", "fa-sliders", "fa-sort", "fa-sort-down", "fa-sort-up", "fa-spinner", "fa-spoon", "fa-square", "fa-square-arrow-up-right", "fa-square-caret-down", "fa-square-caret-left", "fa-square-caret-right", "fa-square-caret-up", "fa-square-check", "fa-square-envelope", "fa-square-h", "fa-square-minus", "fa-square-pen", "fa-square-phone", "fa-square-plus", "fa-square-rss", "fa-square-share-nodes", "fa-star", "fa-star-half", "fa-star-half-stroke", "fa-sterling-sign", "fa-stethoscope", "fa-stop", "fa-street-view", "fa-strikethrough", "fa-subscript", "fa-suitcase", "fa-suitcase-medical", "fa-superscript", "fa-table", "fa-table-cells", "fa-table-cells-large", "fa-table-columns", "fa-table-list", "fa-tablet", "fa-tag", "fa-tags", "fa-taxi", "fa-temperature-empty", "fa-temperature-full", "fa-temperature-half", "fa-temperature-quarter", "fa-temperature-three-quarters", "fa-terminal", "fa-text-height", "fa-text-width", "fa-thermometer", "fa-thumbs-down", "fa-thumbs-up", "fa-thumbtack", "fa-ticket", "fa-toggle-off", "fa-toggle-on", "fa-trademark", "fa-train", "fa-train-subway", "fa-transgender", "fa-trash", "fa-trash-can", "fa-tree", "fa-triangle-exclamation", "fa-trophy", "fa-truck", "fa-truck-medical", "fa-tty", "fa-turkish-lira-sign", "fa-tv", "fa-umbrella", "fa-underline", "fa-universal-access", "fa-unlock", "fa-unlock-keyhole", "fa-up-down-left-right", "fa-upload", "fa-user", "fa-user-doctor", "fa-user-plus", "fa-user-secret", "fa-user-xmark", "fa-users", "fa-utensils", "fa-venus", "fa-venus-double", "fa-venus-mars", "fa-video", "fa-volume-high", "fa-volume-low", "fa-volume-off", "fa-wand-magic", "fa-wheelchair", "fa-wheelchair-move", "fa-wifi", "fa-window-maximize", "fa-window-minimize", "fa-window-restore", "fa-won-sign", "fa-wrench", "fa-xmark", "fa-yen-sign"];
});
