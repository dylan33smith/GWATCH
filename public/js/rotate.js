
    function svg3dOnLoad(t)
    {


alert("hi JS");
//	var Zx = <?php echo json_encode($Zx); ?>;
//	var Zz = <?php echo json_encode($Zz); ?>;



	var e, n, P, r = +document.getElementById("svg3d_" + t).getAttribute("width"),
	    i = +document.getElementById("svg3d_" + t).getAttribute("height"),
	    s = document.getElementById("svg3d_" + t).getAttribute("geometricsolid");
	    
	    "j92" == s ? e = seen.Shapes.j92().scale(.4 * i) :
	    "j93" == s && (e = seen.Shapes.j93().scale(.1 * i)),
	    seen.Colors.randomSurfaces2(e), n = new seen.Scene(
	    {
		model: seen.Models.default().add(e),
		viewport: seen.Viewports.center(r, i)
	    }), (P = seen.Context("svg3d_" + t, n).render()).animate().onBefore(function (t, n)
	    {
		return e.rotx(1e-14 * n).roty(.7 * n * 1e-14)
	    }).start(), new seen.Drag("svg3d_" + t,
	    {
		inertia: !0
	    }).on("drag.rotate", function (t)
	    {
		var n, r;
		return n = (r = seen.Quaternion).xyToTransform.apply(r, t.offsetRelative), e.transform(n), P.render()
	    })
    }! function ()
    {
	var t, e, n, P, r, i, s, a, o, u, h, c, p, l, f, d, m, S, y, g, w, j, v, x, _, b, T, M, C, A, q, E, z, R, F, I, D, L, U, k, O, Z, X, B, N, Y, W, G, H, Q, J, V, K, $, tt, et, nt, Pt, rt, it, st, at, ot, ut, ht, ct, pt, lt, ft, dt, mt, St, yt, gt, wt, jt, vt, xt, _t, bt, Tt, Mt, Ct, At, qt, Et, zt, Rt, Ft, It, Dt, Lt, Ut, kt, Ot, Zt, Xt, Bt, Nt, Yt, Wt, Gt, Ht, Qt, Jt, Vt, Kt, $t, te, ee, ne, Pe, re, ie, se, ae, oe, ue, he, ce, pe, le, fe, de, me, Se, ye, ge, we, je, ve, xe, _e, be, Te, Me, Ce, Ae, qe, Ee, ze, Re, Fe, Ie, De, Le, Ue, ke, Oe, Ze, Xe, Be, Ne, Ye, We, Ge, He, Qe, Je, Ve, Ke, $e, tn, en, nn, Pn, rn, sn, an, on, un, hn, cn, pn, ln, fn, dn, mn, Sn, yn, gn, wn, jn, vn, xn, _n, bn, Tn, Mn, Cn, An, qn, En, zn, Rn, Fn, In, Dn, Ln, Un, kn, On, Zn, Xn, Bn, Nn, Yn, Wn, Gn, Hn, Qn, Jn, Vn, Kn, $n, tP, eP, nP, PP, rP, iP, sP, aP, oP, uP, hP, cP, pP, lP, fP, dP, mP, SP, yP, gP, wP, jP, vP, xP, _P, bP, TP, MP, CP, AP, qP, EP, zP, RP, FP, IP, DP, LP, UP, kP, OP, ZP, XP, BP, NP, YP, WP, GP, HP, QP, JP, VP, KP, $P, tr, er, nr, Pr, rr, ir, Zx, Zz;
	bind = function (t, e)
	    {
		return function ()
		{
		    return t.apply(e, arguments)
		}
	    }, slice = [].slice, extend = function (t, e)
	    {
		for (var n in e) hasProp.call(e, n) && (t[n] = e[n]);

		function P()
		{
		    this.constructor = t
		}
		return P.prototype = e.prototype, t.prototype = new P,
		    t.__super__ = e.prototype, t
	    }, hasProp = {}.hasOwnProperty, M = {}, "undefined" != typeof window && null !== window && (window.seen = M), null != ("undefined" != typeof module && null !== module ? module.exports : void 0) && (module.exports = M), f = 1, M.Util = {
		defaults: function (t, e, n)
		{
		    var P, r;
		    for (P in e) null == t[P] && (t[P] = e[P]);
		    for (P in r = [], n) null == t[P] ? r.push(t[P] = n[P]) : r.push(void 0);
		    return r
		},
		arraysEqual: function (t, e)
		{
		    var n, P, r;
		    if (!t.length === e.length) return !1;
		    for (n = r = 0,
			P = t.length; r < P; n = ++r)
			if (t[n] !== e[n]) return !1;
		    return !0
		},
		uniqueId: function (t)
		{
		    return null == t && (t = ""), t + f++
		},
		element: function (t)
		{
		    return "string" == typeof t ? document.getElementById(t) : t
		}
	    }, M.Events = {
		dispatch: function ()
		{
		    var t, e, n;
		    for (t = new M.Events.Dispatcher, n = 0, e = arguments.length; n < e; n++) t[arguments[n]] = M.Events.Event();
		    return t
		}
	    }, M.Events.Dispatcher = function ()
	    {
		function t()
		{
		    this.on = bind(this.on, this)
		}
		return t.prototype.on = function (t, e)
		{
		    var n, P;
		    return P = "",
			(n = t.indexOf(".")) > 0 && (P = t.substring(n + 1), t = t.substring(0, n)), null != this[t] && this[t].on(P, e), this
		}, t
	    }(), M.Events.Event = function ()
	    {
		var t;
		return (t = function ()
		{
		    var e, n, P, r;
		    for (n in r = [],
			P = t.listenerMap) null != (e = P[n]) ? r.push(e.apply(this, arguments)) : r.push(void 0);
		    return r
		}).listenerMap = {}, t.on = function (e, n)
		{
		    if (delete t.listenerMap[e], null != n) return t.listenerMap[e] = n
		}, t
	    }, t = new Array(16), l = [1, 0, 0, 0, 0, 1, 0, 0, 0, 0, 1, 0, 0, 0, 0, 1],
	    w = [0, 4, 8, 12, 1, 5, 9, 13, 2, 6, 10, 14, 3, 7, 11, 15], M.Matrix = function ()
	    {
		function e(t)
		{
		    this.m = null != t ? t : null, null == this.m && (this.m = l.slice()), this.baked = l
		}
		return e.prototype.copy = function ()
		    {
			return new M.Matrix(this.m.slice())
		    },
		    e.prototype.matrix = function (e)
		    {
			var n, P, r, i, s;
			for (n = t, r = i = 0; i < 4; r = ++i)
			    for (P = s = 0; s < 16; P = s += 4) n[P + r] = e[P] * this.m[r] + e[P + 1] * this.m[4 + r] + e[P + 2] * this.m[8 + r] + e[P + 3] * this.m[12 + r];
			return t = this.m, this.m = n, this
		    }, e.prototype.reset = function ()
		    {
			return this.m = this.baked.slice(), this
		    }, e.prototype.bake = function (t)
		    {
			return this.baked = (null != t ? t : this.m).slice(), this
		    }, e.prototype.multiply = function (t)
		    {
			return this.matrix(t.m)
		    }, e.prototype.transpose = function ()
		    {
			var e, n, P, r, i;
			for (e = t, n = r = 0,
			    P = w.length; r < P; n = ++r) i = w[n], e[n] = this.m[i];
			return t = this.m, this.m = e, this
		    }, e.prototype.rotx = function (t)
		    {
			var e, n, P;
			return n = [1, 0, 0, 0, 0, e = Math.cos(t), -(P = Math.sin(t)), 0, 0, P, e, 0, 0, 0, 0, 1], this.matrix(n)
		    }, e.prototype.roty = function (t)
		    {
			var e, n, P;
			return n = [e = Math.cos(t), 0, P = Math.sin(t), 0, 0, 1, 0, 0, -P, 0, e, 0, 0, 0, 0, 1], this.matrix(n)
		    }, e.prototype.rotz = function (t)
		    {
			var e, n, P;
			return n = [e = Math.cos(t), -(P = Math.sin(t)), 0, 0, P, e, 0, 0, 0, 0, 1, 0, 0, 0, 0, 1], this.matrix(n)
		    },
		    e.prototype.translate = function (t, e, n)
		    {
			var P;
			return null == t && (t = 0), null == e && (e = 0), null == n && (n = 0), P = [1, 0, 0, t, 0, 1, 0, e, 0, 0, 1, n, 0, 0, 0, 1], this.matrix(P)
		    }, e.prototype.scale = function (t, e, n)
		    {
			var P;
			return null == t && (t = 1), null == e && (e = t), null == n && (n = e),
			    P = [t, 0, 0, 0, 0, e, 0, 0, 0, 0, n, 0, 0, 0, 0, 1], this.matrix(P)
		    }, e
	    }(), M.M = function (t)
	    {
		return new M.Matrix(t)
	    }, M.Matrices = {
		identity: function ()
		{
		    return M.M()
		},
		flipX: function ()
		{
		    return M.M().scale(-1, 1, 1)
		},
		flipY: function ()
		{
		    return M.M().scale(1, -1, 1)
		},
		flipZ: function ()
		{
		    return M.M().scale(1, 1, -1)
		}
	    }, M.Transformable = function ()
	    {
		function t()
		{
		    var t, e, n, P, r;
		    for (this.m = new M.Matrix, this.baked = l, r = this, t = function (t)
			{
			    return r[t] = function ()
			    {
				var e;
				return (e = this.m[t]).call.apply(e, [this.m].concat(slice.call(arguments))), this
			    }
			}, n = 0, e = (P = ["scale", "translate", "rotx", "roty", "rotz", "matrix", "reset", "bake"]).length; n < e; n++) t(P[n])
		}
		return t.prototype.transform = function (t)
		{
		    return this.m.multiply(t), this
		}, t
	    }(), M.Point = function ()
	    {
		function t(t, e, n, P)
		{
		    this.x = null != t ? t : 0, this.y = null != e ? e : 0, this.z = null != n ? n : 0, this.w = null != P ? P : 1
		}
		return t.prototype.copy = function ()
		    {
			return new M.Point(this.x, this.y, this.z, this.w)
		    },
		    t.prototype.set = function (t)
		    {
			return this.x = t.x, this.y = t.y, this.z = t.z, this.w = t.w, this
		    }, t.prototype.add = function (t)
		    {
			return this.x += t.x, this.y += t.y, this.z += t.z, this
		    }, t.prototype.subtract = function (t)
		    {
			return this.x -= t.x, this.y -= t.y, this.z -= t.z,
			    this
		    }, t.prototype.translate = function (t, e, n)
		    {
			return this.x += t, this.y += e, this.z += n, this
		    }, t.prototype.multiply = function (t)
		    {
			return this.x *= t, this.y *= t, this.z *= t, this
		    }, t.prototype.divide = function (t)
		    {
			return this.x /= t, this.y /= t, this.z /= t, this
		    },
		    t.prototype.round = function ()
		    {
			return this.x = Math.round(this.x), this.y = Math.round(this.y), this.z = Math.round(this.z), this
		    }, t.prototype.normalize = function ()
		    {
			var t;
			return 0 === (t = this.magnitude()) ? this.set(M.Points.Z()) : this.divide(t), this
		    },
		    t.prototype.perpendicular = function ()
		    {
			var t, e;
			return 0 !== (t = (e = this.copy().cross(M.Points.Z())).magnitude()) ? e.divide(t) : this.copy().cross(M.Points.X()).normalize()
		    }, t.prototype.transform = function (t)
		    {
			var e;
			return (e = d).x = this.x * t.m[0] + this.y * t.m[1] + this.z * t.m[2] + this.w * t.m[3], e.y = this.x * t.m[4] + this.y * t.m[5] + this.z * t.m[6] + this.w * t.m[7], e.z = this.x * t.m[8] + this.y * t.m[9] + this.z * t.m[10] + this.w * t.m[11],
			    e.w = this.x * t.m[12] + this.y * t.m[13] + this.z * t.m[14] + this.w * t.m[15], this.set(e), this
		    }, t.prototype.magnitudeSquared = function ()
		    {
			return this.dot(this)
		    }, t.prototype.magnitude = function ()
		    {
			return Math.sqrt(this.magnitudeSquared())
		    },
		    t.prototype.dot = function (t)
		    {
			return this.x * t.x + this.y * t.y + this.z * t.z
		    }, t.prototype.cross = function (t)
		    {
			var e;
			return (e = d).x = this.y * t.z - this.z * t.y, e.y = this.z * t.x - this.x * t.z, e.z = this.x * t.y - this.y * t.x, this.set(e), this
		    }, t
	    }(), M.P = function (t, e, n, P)
	    {
		return new M.Point(t, e, n, P)
	    }, d = M.P(), M.Points = {
		X: function ()
		{
		    return M.P(1, 0, 0)
		},
		Y: function ()
		{
		    return M.P(0, 1, 0)
		},
		Z: function ()
		{
		    return M.P(0, 0, 1)
		},
		ZERO: function ()
		{
		    return M.P(0, 0, 0)
		}
	    }, M.Quaternion = function ()
	    {
		function t()
		{
		    this.q = M.P.apply(M, arguments)
		}
		return t.pixelsPerRadian = 150, t.xyToTransform = function (t, e)
		    {
			var n, P;
			return n = M.Quaternion.pointAngle(M.Points.Y(), t / M.Quaternion.pixelsPerRadian),
			    P = M.Quaternion.pointAngle(M.Points.X(), e / M.Quaternion.pixelsPerRadian), n.multiply(P).toMatrix()
		    }, t.axisAngle = function (t, e, n, P)
		    {
			var r, i;
			return r = Math.sin(P / 2), i = Math.cos(P / 2), new M.Quaternion(r * t, r * e, r * n, i)
		    }, t.pointAngle = function (t, e)
		    {
			var n, P;
			return n = Math.sin(e / 2), P = Math.cos(e / 2), new M.Quaternion(n * t.x, n * t.y, n * t.z, P)
		    }, t.prototype.multiply = function (t)
		    {
			var e, n;
			return (e = M.P()).w = this.q.w * t.q.w - this.q.x * t.q.x - this.q.y * t.q.y - this.q.z * t.q.z,
			    e.x = this.q.w * t.q.x + this.q.x * t.q.w + this.q.y * t.q.z - this.q.z * t.q.y, e.y = this.q.w * t.q.y + this.q.y * t.q.w + this.q.z * t.q.x - this.q.x * t.q.z, e.z = this.q.w * t.q.z + this.q.z * t.q.w + this.q.x * t.q.y - this.q.y * t.q.x, (n = new M.Quaternion).q = e, n
		    },
		    t.prototype.toMatrix = function ()
		    {
			var t;
			return (t = new Array(16))[0] = 1 - 2 * (this.q.y * this.q.y + this.q.z * this.q.z), t[1] = 2 * (this.q.x * this.q.y - this.q.w * this.q.z), t[2] = 2 * (this.q.x * this.q.z + this.q.w * this.q.y), t[3] = 0,
			    t[4] = 2 * (this.q.x * this.q.y + this.q.w * this.q.z), t[5] = 1 - 2 * (this.q.x * this.q.x + this.q.z * this.q.z), t[6] = 2 * (this.q.y * this.q.z - this.q.w * this.q.x), t[7] = 0, t[8] = 2 * (this.q.x * this.q.z - this.q.w * this.q.y), t[9] = 2 * (this.q.y * this.q.z + this.q.w * this.q.x),
			    t[10] = 1 - 2 * (this.q.x * this.q.x + this.q.y * this.q.y), t[11] = 0, t[12] = 0, t[13] = 0, t[14] = 0, t[15] = 1, M.M(t)
		    }, t
	    }(), M.Bounds = function ()
	    {
		function t()
		{
		    this.maxZ = bind(this.maxZ, this), this.maxY = bind(this.maxY, this), this.maxX = bind(this.maxX, this),
			this.minZ = bind(this.minZ, this), this.minY = bind(this.minY, this), this.minX = bind(this.minX, this), this.depth = bind(this.depth, this), this.height = bind(this.height, this), this.width = bind(this.width, this), this.min = null, this.max = null
		}
		return t.points = function (t)
		    {
			var e, n, P, r;
			for (e = new M.Bounds, P = 0, n = t.length; P < n; P++) r = t[P], e.add(r);
			return e
		    }, t.xywh = function (t, e, n, P)
		    {
			return M.Boundses.xyzwhd(t, e, 0, n, P, 0)
		    }, t.xyzwhd = function (t, e, n, P, r, i)
		    {
			var s;
			return (s = new M.Bounds).add(M.P(t, e, n)), s.add(M.P(t + P, e + r, n + i)), s
		    }, t.prototype.copy = function ()
		    {
			var t, e, n;
			return (t = new M.Bounds).min = null != (e = this.min) ? e.copy() : void 0, t.max = null != (n = this.max) ? n.copy() : void 0, t
		    }, t.prototype.add = function (t)
		    {
			return null == this.min || null == this.max ? (this.min = t.copy(), this.max = t.copy()) : (this.min.x = Math.min(this.min.x, t.x), this.min.y = Math.min(this.min.y, t.y), this.min.z = Math.min(this.min.z, t.z), this.max.x = Math.max(this.max.x, t.x),
			    this.max.y = Math.max(this.max.y, t.y), this.max.z = Math.max(this.max.z, t.z)), this
		    }, t.prototype.valid = function ()
		    {
			return null != this.min && null != this.max
		    }, t.prototype.intersect = function (t)
		    {
			return this.valid() && t.valid() ? (this.min = M.P(Math.max(this.min.x, t.min.x), Math.max(this.min.y, t.min.y), Math.max(this.min.z, t.min.z)), this.max = M.P(Math.min(this.max.x, t.max.x), Math.min(this.max.y, t.max.y), Math.min(this.max.z, t.max.z)),
			    (this.min.x > this.max.x || this.min.y > this.max.y || this.min.z > this.max.z) && (this.min = null, this.max = null)) : (this.min = null, this.max = null), this
		    }, t.prototype.pad = function (t, e, n)
		    {
			var P;
			return this.valid() && (null == e && (e = t), null == n && (n = e),
			    P = M.P(t, e, n), this.min.subtract(P), this.max.add(P)), this
		    }, t.prototype.reset = function ()
		    {
			return this.min = null, this.max = null, this
		    }, t.prototype.contains = function (t)
		    {
			return !!this.valid() && (!(this.min.x > t.x || this.max.x < t.x) && (!(this.min.y > t.y || this.max.y < t.y) && !(this.min.z > t.z || this.max.z < t.z)))
		    }, t.prototype.center = function ()
		    {
			return M.P(this.minX() + this.width() / 2, this.minY() + this.height() / 2, this.minZ() + this.depth() / 2)
		    }, t.prototype.width = function ()
		    {
			return this.maxX() - this.minX()
		    }, t.prototype.height = function ()
		    {
			return this.maxY() - this.minY()
		    },
		    t.prototype.depth = function ()
		    {
			return this.maxZ() - this.minZ()
		    }, t.prototype.minX = function ()
		    {
			var t, e;
			return null != (t = null != (e = this.min) ? e.x : void 0) ? t : 0
		    }, t.prototype.minY = function ()
		    {
			var t, e;
			return null != (t = null != (e = this.min) ? e.y : void 0) ? t : 0
		    },
		    t.prototype.minZ = function ()
		    {
			var t, e;
			return null != (t = null != (e = this.min) ? e.z : void 0) ? t : 0
		    }, t.prototype.maxX = function ()
		    {
			var t, e;
			return null != (t = null != (e = this.max) ? e.x : void 0) ? t : 0
		    }, t.prototype.maxY = function ()
		    {
			var t, e;
			return null != (t = null != (e = this.max) ? e.y : void 0) ? t : 0
		    }, t.prototype.maxZ = function ()
		    {
			var t, e;
			return null != (t = null != (e = this.max) ? e.z : void 0) ? t : 0
		    }, t
	    }(), M.Color = function ()
	    {
		function t(t, e, n, P)
		{
		    this.r = null != t ? t : 0, this.g = null != e ? e : 0,
			this.b = null != n ? n : 0, this.a = null != P ? P : 255
		}
		return t.prototype.copy = function ()
		    {
			return new M.Color(this.r, this.g, this.b, this.a)
		    }, t.prototype.scale = function (t)
		    {
			return this.r *= t, this.g *= t, this.b *= t, this
		    }, t.prototype.offset = function (t)
		    {
			return this.r += t, this.g += t, this.b += t, this
		    }, t.prototype.clamp = function (t, e)
		    {
			return null == t && (t = 0), null == e && (e = 255), this.r = Math.min(e, Math.max(t, this.r)), this.g = Math.min(e, Math.max(t, this.g)), this.b = Math.min(e, Math.max(t, this.b)), this
		    },
		    t.prototype.minChannels = function (t)
		    {
			return this.r = Math.min(t.r, this.r), this.g = Math.min(t.g, this.g), this.b = Math.min(t.b, this.b), this
		    }, t.prototype.addChannels = function (t)
		    {
			return this.r += t.r, this.g += t.g, this.b += t.b, this
		    },
		    t.prototype.multiplyChannels = function (t)
		    {
			return this.r *= t.r, this.g *= t.g, this.b *= t.b, this
		    }, t.prototype.hex = function ()
		    {
			var t;
			for (t = (this.r << 16 | this.g << 8 | this.b).toString(16); t.length < 6;) t = "0" + t;
			return "#" + t
		    }, t.prototype.style = function ()
		    {
			return "rgba(" + this.r + "," + this.g + "," + this.b + "," + this.a + ")"
		    }, t
	    }(), M.Colors = {
		CSS_RGBA_STRING_REGEX: /rgb(a)?\(([0-9.]+),([0-9.]+),*([0-9.]+)(,([0-9.]+))?\)/,
		parse: function (t)
		{
		    var e, n;
		    return "#" === t.charAt(0) && 7 === t.length ? M.Colors.hex(t) : 0 === t.indexOf("rgb") ? null == (n = M.Colors.CSS_RGBA_STRING_REGEX.exec(t)) ? M.Colors.black() : (e = null != n[6] ? Math.round(255 * parseFloat(n[6])) : void 0,
			new M.Color(parseFloat(n[2]), parseFloat(n[3]), parseFloat(n[4]), e)) : M.Colors.black()
		},
		rgb: function (t, e, n, P)
		{
		    return null == P && (P = 255), new M.Color(t, e, n, P)
		},
		hex: function (t)
		{
		    return "#" === t.charAt(0) && (t = t.substring(1)),
			new M.Color(parseInt(t.substring(0, 2), 16), parseInt(t.substring(2, 4), 16), parseInt(t.substring(4, 6), 16))
		},
		hsl: function (t, e, n, P)
		{
		    var r, i, s, a, o, u;
		    return null == P && (P = 1), u = i = r = 0, 0 === e ? u = i = r = n : (u = (s = function (t, e, n)
		    {
			return n < 0 ? n += 1 : n > 1 && (n -= 1),
			    n < 1 / 6 ? t + 6 * (e - t) * n : n < .5 ? e : n < 2 / 3 ? t + (e - t) * (2 / 3 - n) * 6 : t
		    })(a = 2 * n - (o = n < .5 ? n * (1 + e) : n + e - n * e), o, t + 1 / 3), i = s(a, o, t), r = s(a, o, t - 1 / 3)), new M.Color(255 * u, 255 * i, 255 * r, 255 * P)
		},
		randomSurfaces: function (t, e, n)
		{
		    var P, r, i, s, a;
		    for (null == e && (e = .5), null == n && (n = .4),
			s = [], r = 0, P = (i = t.surfaces).length; r < P; r++) a = i[r], s.push(a.fill(M.Colors.hsl(Math.random(), e, n)));
		    return s
		},
		randomSurfaces2: function (t, e, n, P)
		{
		    var r, i, s, a, o, u;
		    for (null == e && (e = .03), null == n && (n = .5), null == P && (P = .4), r = Math.random(), o = [], s = 0,
			i = (a = t.surfaces).length; s < i; s++)
		    {
			for (u = a[s], r += (Math.random() - .5) * e; r < 0;) r += 1;
			for (; r > 1;) r -= 1;
			o.push(u.fill(M.Colors.hsl(r, .5, .4)))
		    }
		    return o
		},
		randomShape: function (t, e, n)
		{
		    return null == e && (e = .5), null == n && (n = .4),
			t.fill(new M.Material(M.Colors.hsl(Math.random(), e, n)))
		},
		black: function ()
		{
		    return this.hex("#000000")
		},
		white: function ()
		{
		    return this.hex("#FFFFFF")
		},
		gray: function ()
		{
		    return this.hex("#888888")
		}
	    }, M.C = function (t, e, n, P)
	    {
		return new M.Color(t, e, n, P)
	    }, M.Material = function ()
	    {
		function t(t, e)
		{
		    this.color = t, null == e && (e = {}), M.Util.defaults(this, e, this.defaults)
		}
		return t.create = function (t)
		{
		    return t instanceof M.Material ? t : t instanceof M.Color ? new M.Material(t) : "string" == typeof t ? new M.Material(M.Colors.parse(t)) : new M.Material
		}, t.prototype.defaults = {
		    color: M.Colors.gray(),
		    metallic: !1,
		    specularColor: M.Colors.white(),
		    specularExponent: 15,
		    shader: null
		}, t.prototype.render = function (t, e, n)
		{
		    var P, r;
		    return (P = (null != (r = this.shader) ? r : e).shade(t, n, this)).a = this.color.a, P
		}, t
	    }(), M.Light = function (t)
	    {
		function e(t, n)
		{
		    this.type = t,
			e.__super__.constructor.apply(this, arguments), M.Util.defaults(this, n, this.defaults), this.id = M.Util.uniqueId("l")
		}
		return extend(e, t), e.prototype.defaults = {
		    point: M.P(),
		    color: M.Colors.white(),
		    intensity: .01,
		    normal: M.P(1, -1, -1).normalize(),
		    enabled: !0
		}, e.prototype.render = function ()
		{
		    return this.colorIntensity = this.color.copy().scale(this.intensity)
		}, e
	    }(M.Transformable), M.Lights = {
		point: function (t)
		{
		    return new M.Light("point", t)
		},
		directional: function (t)
		{
		    return new M.Light("directional", t)
		},
		ambient: function (t)
		{
		    return new M.Light("ambient", t)
		}
	    }, s = M.Points.Z(), M.ShaderUtils = {
		applyDiffuse: function (t, e, n, P, r)
		{
		    var i;
		    if ((i = n.dot(P)) > 0) return t.addChannels(e.colorIntensity.copy().scale(i))
		},
		applyDiffuseAndSpecular: function (t, e, n, P, r)
		{
		    var i, a, o, u;
		    if ((i = n.dot(P)) > 0) return t.addChannels(e.colorIntensity.copy().scale(i)), a = P.copy().multiply(2 * i).subtract(n), u = Math.pow(.5 + a.dot(s), r.specularExponent),
			o = r.specularColor.copy().scale(u * e.intensity / 255), t.addChannels(o)
		},
		applyAmbient: function (t, e)
		{
		    return t.addChannels(e.colorIntensity)
		}
	    }, M.Shader = function ()
	    {
		function t()
		{}
		return t.prototype.shade = function (t, e, n) {}, t
	    }(), S = function (t)
	    {
		function e()
		{
		    return e.__super__.constructor.apply(this, arguments)
		}
		return extend(e, t), e.prototype.shade = function (t, e, n)
		{
		    var P, r, i, s, a;
		    for (P = new M.Color, a = 0, r = t.length; a < r; a++) switch ((i = t[a]).type)
		    {
			case "point":
			    s = i.point.copy().subtract(e.barycenter).normalize(), M.ShaderUtils.applyDiffuseAndSpecular(P, i, s, e.normal, n);
			    break;
			case "directional":
			    M.ShaderUtils.applyDiffuseAndSpecular(P, i, i.normal, e.normal, n);
			    break;
			case "ambient":
			    M.ShaderUtils.applyAmbient(P, i)
		    }
		    return P.multiplyChannels(n.color), n.metallic && P.minChannels(n.specularColor), P.clamp(0, 255), P
		}, e
	    }(M.Shader), r = function (t)
	    {
		function e()
		{
		    return e.__super__.constructor.apply(this, arguments)
		}
		return extend(e, t),
		    e.prototype.shade = function (t, e, n)
		    {
			var P, r, i, s, a;
			for (P = new M.Color, a = 0, r = t.length; a < r; a++) switch ((i = t[a]).type)
			{
			    case "point":
				s = i.point.copy().subtract(e.barycenter).normalize(), M.ShaderUtils.applyDiffuse(P, i, s, e.normal, n);
				break;
			    case "directional":
				M.ShaderUtils.applyDiffuse(P, i, i.normal, e.normal, n);
				break;
			    case "ambient":
				M.ShaderUtils.applyAmbient(P, i)
			}
			return P.multiplyChannels(n.color).clamp(0, 255), P
		    }, e
	    }(M.Shader), e = function (t)
	    {
		function e()
		{
		    return e.__super__.constructor.apply(this, arguments)
		}
		return extend(e, t), e.prototype.shade = function (t, e, n)
		{
		    var P, r, i, s;
		    for (P = new M.Color, s = 0, r = t.length; s < r; s++) switch ((i = t[s]).type)
		    {
			case "ambient":
			    M.ShaderUtils.applyAmbient(P, i)
		    }
		    return P.multiplyChannels(n.color).clamp(0, 255), P
		}, e
	    }(M.Shader), a = function (t)
	    {
		function e()
		{
		    return e.__super__.constructor.apply(this, arguments)
		}
		return extend(e, t), e.prototype.shade = function (t, e, n)
		{
		    return n.color
		}, e
	    }(M.Shader), M.Shaders = {
		phong: function ()
		{
		    return new S
		},
		diffuse: function ()
		{
		    return new r
		},
		ambient: function ()
		{
		    return new e
		},
		flat: function ()
		{
		    return new a
		}
	    }, M.Affine = {
		ORTHONORMAL_BASIS: function ()
		{
		    return [M.P(0, 0, 0), M.P(20, 0, 0), M.P(0, 20, 0)]
		},
		INITIAL_STATE_MATRIX: [
		    [20, 0, 1, 0, 0, 0],
		    [0, 20, 1, 0, 0, 0],
		    [0, 0, 1, 0, 0, 0],
		    [0, 0, 0, 20, 0, 1],
		    [0, 0, 0, 0, 20, 1],
		    [0, 0, 0, 0, 0, 1]
		],
		solveForAffineTransform: function (t)
		{
		    var e, n, P, r, i, s, a, o, u, h;
		    for (e = M.Affine.INITIAL_STATE_MATRIX,
			n = [t[1].x, t[2].x, t[0].x, t[1].y, t[2].y, t[0].y], h = new Array(6), P = s = (i = e.length) - 1; s >= 0; P = s += -1)
		    {
			for (h[P] = n[P], r = u = a = P + 1, o = i; a <= o ? u < o : u > o; r = a <= o ? ++u : --u) h[P] -= e[P][r] * h[r];
			h[P] /= e[P][P]
		    }
		    return h
		}
	    }, M.RenderContext = function ()
	    {
		function t()
		{
		    this.render = bind(this.render, this), this.layers = []
		}
		return t.prototype.render = function ()
		{
		    var t, e, n, P;
		    for (this.reset(), n = 0, e = (P = this.layers).length; n < e; n++)(t = P[n]).context.reset(), t.layer.render(t.context), t.context.cleanup();
		    return this.cleanup(), this
		}, t.prototype.animate = function ()
		{
		    return new M.RenderAnimator(this)
		}, t.prototype.layer = function (t)
		{
		    return this.layers.push(
		    {
			layer: t,
			context: this
		    }), this
		}, t.prototype.sceneLayer = function (t)
		{
		    return this.layer(new M.SceneLayer(t)), this
		}, t.prototype.reset = function () {}, t.prototype.cleanup = function () {}, t
	    }(), M.RenderLayerContext = function ()
	    {
		function t()
		{}
		return t.prototype.path = function () {}, t.prototype.rect = function () {},
		    t.prototype.circle = function () {}, t.prototype.text = function () {}, t.prototype.reset = function () {}, t.prototype.cleanup = function () {}, t
	    }(), M.Context = function (t, e)
	    {
		var n, P, r;
		return null == e && (e = null),
		    r = null != (P = M.Util.element(t)) ? P.tagName.toUpperCase() : void 0, null != (n = function ()
		    {
			switch (r)
			{
			    case "SVG":
			    case "G":
				return new M.SvgRenderContext(t);
			    case "CANVAS":
				return new M.CanvasRenderContext(t)
			}
		    }()) && null != e && n.sceneLayer(e), n
	    },
	    M.Painter = function ()
	    {
		function t()
		{}
		return t.prototype.paint = function (t, e) {}, t
	    }(), M.PathPainter = function (t)
	    {
		function e()
		{
		    return e.__super__.constructor.apply(this, arguments)
		}
		return extend(e, t), e.prototype.paint = function (t, e)
		{
		    var n, P, r;
		    if (n = e.path().path(t.projected.points), null != t.fill && n.fill(
			{
			    fill: null == t.fill ? "none" : t.fill.hex(),
			    "fill-opacity": null == (null != (P = t.fill) ? P.a : void 0) ? 1 : t.fill.a / 255
			}), null != t.stroke) return n.draw(
		    {
			fill: "none",
			stroke: null == t.stroke ? "none" : t.stroke.hex(),
			"stroke-width": null != (r = t.surface["stroke-width"]) ? r : 1
		    })
		}, e
	    }(M.Painter), M.TextPainter = function (t)
	    {
		function e()
		{
		    return e.__super__.constructor.apply(this, arguments)
		}
		return extend(e, t),
		    e.prototype.paint = function (t, e)
		    {
			var n, P, r;
			return P = {
				fill: null == t.fill ? "none" : t.fill.hex(),
				font: t.surface.font,
				"text-anchor": null != (n = t.surface.anchor) ? n : "middle"
			    }, r = M.Affine.solveForAffineTransform(t.projected.points),
			    e.text().fillText(r, t.surface.text, P)
		    }, e
	    }(M.Painter), M.Painters = {
		path: new M.PathPainter,
		text: new M.TextPainter
	    }, P = M.Points.Z(), M.RenderModel = function ()
	    {
		function t(t, e, n, P)
		{
		    this.surface = t, this.transform = e, this.projection = n, this.viewport = P,
			this.points = this.surface.points, this.transformed = this._initRenderData(), this.projected = this._initRenderData(), this._update()
		}
		return t.prototype.update = function (t, e, n)
		    {
			if (this.surface.dirty || !M.Util.arraysEqual(t.m, this.transform.m) || !M.Util.arraysEqual(e.m, this.projection.m) || !M.Util.arraysEqual(n.m, this.viewport.m)) return this.transform = t, this.projection = e, this.viewport = n, this._update()
		    },
		    t.prototype._update = function ()
		    {
			var t, e;
			return this._math(this.transformed, this.points, this.transform, !1), t = this.transformed.points.map((e = this, function (t)
			    {
				return t.copy().transform(e.projection)
			    })), this.inFrustrum = this._checkFrustrum(t),
			    this._math(this.projected, t, this.viewport, !0), this.surface.dirty = !1
		    }, t.prototype._checkFrustrum = function (t)
		    {
			var e, n;
			for (n = 0, e = t.length; n < e; n++)
			    if (t[n].z <= -2) return !1;
			return !0
		    }, t.prototype._initRenderData = function ()
		    {
			var t;
			return {
			    points: function ()
			    {
				var e, n, P, r;
				for (r = [], n = 0, e = (P = this.points).length; n < e; n++) t = P[n], r.push(t.copy());
				return r
			    }.call(this),
			    bounds: new M.Bounds,
			    barycenter: M.P(),
			    normal: M.P(),
			    v0: M.P(),
			    v1: M.P()
			}
		    }, t.prototype._math = function (t, e, n, r)
		    {
			var i, s, a, o, u, h, c, p, l, f, d;
			for (null == r && (r = !1), s = h = 0, a = e.length; h < a; s = ++h) c = e[s], (f = t.points[s]).set(c).transform(n), r && f.divide(f.w);
			for (t.barycenter.multiply(0), d = 0, o = (p = t.points).length; d < o; d++) c = p[d], t.barycenter.add(c);
			for (t.barycenter.divide(t.points.length), t.bounds.reset(), i = 0, u = (l = t.points).length; i < u; i++) c = l[i], t.bounds.add(c);
			return t.points.length < 2 ? (t.v0.set(P), t.v1.set(P), t.normal.set(P)) : (t.v0.set(t.points[1]).subtract(t.points[0]),
			    t.v1.set(t.points[e.length - 1]).subtract(t.points[0]), t.normal.set(t.v0).cross(t.v1).normalize())
		    }, t
	    }(), M.LightRenderModel = function ()
	    {
		return function (t, e)
		{
		    var n;
		    this.light = t,
			this.colorIntensity = this.light.color.copy().scale(this.light.intensity), this.type = this.light.type, this.intensity = this.light.intensity, this.point = this.light.point.copy().transform(e), n = M.Points.ZERO().transform(e),
			this.normal = this.light.normal.copy().transform(e).subtract(n).normalize()
		}
	    }(), M.RenderLayer = function ()
	    {
		function t()
		{
		    this.render = bind(this.render, this)
		}
		return t.prototype.render = function (t) {}, t
	    }(), M.SceneLayer = function (t)
	    {
		function e(t)
		{
		    this.scene = t, this.render = bind(this.render, this)
		}
		return extend(e, t), e.prototype.render = function (t)
		{
		    var e, n, P, r, i;
		    for (i = [], n = 0, e = (P = this.scene.render()).length; n < e; n++) r = P[n], i.push(r.surface.painter.paint(r, t));
		    return i
		}, e
	    }(M.RenderLayer),
	    M.FillLayer = function (t)
	    {
		function e(t, e, n)
		{
		    this.width = null != t ? t : 500, this.height = null != e ? e : 500, this.fill = null != n ? n : "#EEE", this.render = bind(this.render, this)
		}
		return extend(e, t), e.prototype.render = function (t)
		{
		    return t.rect().rect(this.width, this.height).fill(
		    {
			fill: this.fill
		    })
		}, e
	    }(M.RenderLayer), j = function (t)
	    {
		return document.createElementNS("http://www.w3.org/2000/svg", t)
	    }, M.SvgStyler = function ()
	    {
		function t(t)
		{
		    this.elementFactory = t
		}
		return t.prototype._attributes = {}, t.prototype._svgTag = "g", t.prototype.clear = function ()
		{
		    return this._attributes = {}, this
		}, t.prototype.fill = function (t)
		{
		    return null == t && (t = {}), this._paint(t), this
		}, t.prototype.draw = function (t)
		{
		    return null == t && (t = {}), this._paint(t), this
		}, t.prototype._paint = function (t)
		{
		    var e, n, P, r, i;
		    for (n in e = this.elementFactory(this._svgTag), r = "", t) r += n + ":" + (i = t[n]) + ";";
		    for (n in e.setAttribute("style", r), P = this._attributes) i = P[n],
			e.setAttribute(n, i);
		    return e
		}, t
	    }(), M.SvgPathPainter = function (t)
	    {
		function e()
		{
		    return e.__super__.constructor.apply(this, arguments)
		}
		return extend(e, t), e.prototype._svgTag = "path", e.prototype.path = function (t)
		{
		    return this._attributes.d = "M" + t.map(function (t)
		    {
			return t.x + " " + t.y
		    }).join("L"), this
		}, e
	    }(M.SvgStyler), M.SvgTextPainter = function ()
	    {
		function t(t)
		{
		    this.elementFactory = t
		}
		return t.prototype._svgTag = "text", t.prototype.fillText = function (t, e, n)
		{
		    var P, r, i, s;
		    for (r in null == n && (n = {}), (P = this.elementFactory(this._svgTag)).setAttribute("transform", "matrix(" + t[0] + " " + t[3] + " " + -t[1] + " " + -t[4] + " " + t[2] + " " + t[5] + ")"), i = "", n) null != (s = n[r]) && (i += r + ":" + s + ";");
		    return P.setAttribute("style", i),
			P.textContent = e
		}, t
	    }(), M.SvgRectPainter = function (t)
	    {
		function e()
		{
		    return e.__super__.constructor.apply(this, arguments)
		}
		return extend(e, t), e.prototype._svgTag = "rect", e.prototype.rect = function (t, e)
		{
		    return this._attributes.width = t,
			this._attributes.height = e, this
		}, e
	    }(M.SvgStyler), M.SvgCirclePainter = function (t)
	    {
		function e()
		{
		    return e.__super__.constructor.apply(this, arguments)
		}
		return extend(e, t), e.prototype._svgTag = "circle", e.prototype.circle = function (t, e)
		{
		    return this._attributes.cx = t.x, this._attributes.cy = t.y, this._attributes.r = e, this
		}, e
	    }(M.SvgStyler), M.SvgLayerRenderContext = function (t)
	    {
		function e(t)
		{
		    this.group = t, this._elementFactory = bind(this._elementFactory, this),
			this.pathPainter = new M.SvgPathPainter(this._elementFactory), this.textPainter = new M.SvgTextPainter(this._elementFactory), this.circlePainter = new M.SvgCirclePainter(this._elementFactory),
			this.rectPainter = new M.SvgRectPainter(this._elementFactory), this._i = 0
		}
		return extend(e, t), e.prototype.path = function ()
		    {
			return this.pathPainter.clear()
		    }, e.prototype.rect = function ()
		    {
			return this.rectPainter.clear()
		    },
		    e.prototype.circle = function ()
		    {
			return this.circlePainter.clear()
		    }, e.prototype.text = function ()
		    {
			return this.textPainter
		    }, e.prototype.reset = function ()
		    {
			return this._i = 0
		    }, e.prototype.cleanup = function ()
		    {
			var t, e;
			for (t = this.group.childNodes,
			    e = []; this._i < t.length;) t[this._i].setAttribute("style", "display: none;"), e.push(this._i++);
			return e
		    }, e.prototype._elementFactory = function (t)
		    {
			var e, n, P;
			return e = this.group.childNodes, this._i >= e.length ? (P = j(t), this.group.appendChild(P),
			    this._i++, P) : (n = e[this._i]).tagName === t ? (this._i++, n) : (P = j(t), this.group.replaceChild(P, n), this._i++, P)
		    }, e
	    }(M.RenderLayerContext), M.SvgRenderContext = function (t)
	    {
		function e(t)
		{
		    this.svg = t, e.__super__.constructor.call(this),
			this.svg = M.Util.element(this.svg)
		}
		return extend(e, t), e.prototype.layer = function (t)
		{
		    var e;
		    return this.svg.appendChild(e = j("g")), this.layers.push(
		    {
			layer: t,
			context: new M.SvgLayerRenderContext(e)
		    }), this
		}, e
	    }(M.RenderContext),
	    M.SvgContext = function (t, e)
	    {
		var n;
		return n = new M.SvgRenderContext(t), null != e && n.sceneLayer(e), n
	    }, M.CanvasStyler = function ()
	    {
		function t(t)
		{
		    this.ctx = t
		}
		return t.prototype.draw = function (t)
		{
		    return null == t && (t = {}),
			null != t.stroke && (this.ctx.strokeStyle = t.stroke), null != t["stroke-width"] && (this.ctx.lineWidth = t["stroke-width"]), null != t["text-anchor"] && (this.ctx.textAlign = t["text-anchor"]), this.ctx.stroke(), this
		}, t.prototype.fill = function (t)
		{
		    return null == t && (t = {}), null != t.fill && (this.ctx.fillStyle = t.fill), null != t["text-anchor"] && (this.ctx.textAlign = t["text-anchor"]), t["fill-opacity"] && (this.ctx.globalAlpha = t["fill-opacity"]), this.ctx.fill(), this
		}, t
	    }(),
	    M.CanvasPathPainter = function (t)
	    {
		function e()
		{
		    return e.__super__.constructor.apply(this, arguments)
		}
		return extend(e, t), e.prototype.path = function (t)
		{
		    var e, n, P, r;
		    for (this.ctx.beginPath(), e = P = 0, n = t.length; P < n; e = ++P) r = t[e],
			0 === e ? this.ctx.moveTo(r.x, r.y) : this.ctx.lineTo(r.x, r.y);
		    return this.ctx.closePath(), this
		}, e
	    }(M.CanvasStyler), M.CanvasRectPainter = function (t)
	    {
		function e()
		{
		    return e.__super__.constructor.apply(this, arguments)
		}
		return extend(e, t),
		    e.prototype.rect = function (t, e)
		    {
			return this.ctx.rect(0, 0, t, e), this
		    }, e
	    }(M.CanvasStyler), M.CanvasCirclePainter = function (t)
	    {
		function e()
		{
		    return e.__super__.constructor.apply(this, arguments)
		}
		return extend(e, t), e.prototype.circle = function (t, e)
		{
		    return this.ctx.beginPath(), this.ctx.arc(t.x, t.y, e, 0, 2 * Math.PI, !0), this
		}, e
	    }(M.CanvasStyler), M.CanvasTextPainter = function ()
	    {
		function t(t)
		{
		    this.ctx = t
		}
		return t.prototype.fillText = function (t, e, n)
		{
		    return null == n && (n = {}), this.ctx.save(),
			this.ctx.setTransform(t[0], t[3], -t[1], -t[4], t[2], t[5]), null != n.font && (this.ctx.font = n.font), null != n.fill && (this.ctx.fillStyle = n.fill), null != n["text-anchor"] && (this.ctx.textAlign = this._cssToCanvasAnchor(n["text-anchor"])),
			this.ctx.fillText(e, 0, 0), this.ctx.restore(), this
		}, t.prototype._cssToCanvasAnchor = function (t)
		{
		    return "middle" === t ? "center" : t
		}, t
	    }(), M.CanvasLayerRenderContext = function (t)
	    {
		function e(t)
		{
		    this.ctx = t,
			this.pathPainter = new M.CanvasPathPainter(this.ctx), this.ciclePainter = new M.CanvasCirclePainter(this.ctx), this.textPainter = new M.CanvasTextPainter(this.ctx), this.rectPainter = new M.CanvasRectPainter(this.ctx)
		}
		return extend(e, t),
		    e.prototype.path = function ()
		    {
			return this.pathPainter
		    }, e.prototype.rect = function ()
		    {
			return this.rectPainter
		    }, e.prototype.circle = function ()
		    {
			return this.ciclePainter
		    }, e.prototype.text = function ()
		    {
			return this.textPainter
		    }, e
	    }(M.RenderLayerContext),
	    M.CanvasRenderContext = function (t)
	    {
		function e(t)
		{
		    this.el = t, e.__super__.constructor.call(this), this.el = M.Util.element(this.el), this.ctx = this.el.getContext("2d")
		}
		return extend(e, t), e.prototype.layer = function (t)
		{
		    return this.layers.push(
		    {
			layer: t,
			context: new M.CanvasLayerRenderContext(this.ctx)
		    }), this
		}, e.prototype.reset = function ()
		{
		    return this.ctx.setTransform(1, 0, 0, 1, 0, 0), this.ctx.clearRect(0, 0, this.el.width, this.el.height)
		}, e
	    }(M.RenderContext), M.CanvasContext = function (t, e)
	    {
		var n;
		return n = new M.CanvasRenderContext(t), null != e && n.sceneLayer(e), n
	    }, M.WindowEvents = (ir = M.Events.dispatch("mouseMove", "mouseDown", "mouseUp", "touchStart", "touchMove", "touchEnd", "touchCancel"),
		"undefined" != typeof window && null !== window && (window.addEventListener("mouseup", ir.mouseUp, !0), window.addEventListener("mousedown", ir.mouseDown, !0), window.addEventListener("mousemove", ir.mouseMove, !0),
		    window.addEventListener("touchstart", ir.touchStart, !0), window.addEventListener("touchmove", ir.touchMove, !0), window.addEventListener("touchend", ir.touchEnd, !0), window.addEventListener("touchcancel", ir.touchCancel, !0)),
		{
		    on: ir.on
		}),
	    M.MouseEvents = function ()
	    {
		function t(t, e)
		{
		    this.el = t, this._onMouseWheel = bind(this._onMouseWheel, this), this._onMouseUp = bind(this._onMouseUp, this), this._onMouseDown = bind(this._onMouseDown, this), this._onMouseMove = bind(this._onMouseMove, this),
			M.Util.defaults(this, e, this.defaults), this.el = M.Util.element(this.el), this._uid = M.Util.uniqueId("mouser-"), this.dispatch = M.Events.dispatch("dragStart", "drag", "dragEnd", "mouseMove", "mouseDown", "mouseUp", "mouseWheel"),
			this.on = this.dispatch.on, this._mouseDown = !1, this.attach()
		}
		return t.prototype.attach = function ()
		    {
			return this.el.addEventListener("touchstart", this._onMouseDown), this.el.addEventListener("mousedown", this._onMouseDown),
			    this.el.addEventListener("mousewheel", this._onMouseWheel)
		    }, t.prototype.detach = function ()
		    {
			return this.el.removeEventListener("touchstart", this._onMouseDown), this.el.removeEventListener("mousedown", this._onMouseDown),
			    this.el.removeEventListener("mousewheel", this._onMouseWheel)
		    }, t.prototype._onMouseMove = function (t)
		    {
			if (this.dispatch.mouseMove(t), t.preventDefault(), t.stopPropagation(), this._mouseDown) return this.dispatch.drag(t)
		    },
		    t.prototype._onMouseDown = function (t)
		    {
			return this._mouseDown = !0, M.WindowEvents.on("mouseUp." + this._uid, this._onMouseUp), M.WindowEvents.on("mouseMove." + this._uid, this._onMouseMove), M.WindowEvents.on("touchEnd." + this._uid, this._onMouseUp),
			    M.WindowEvents.on("touchCancel." + this._uid, this._onMouseUp), M.WindowEvents.on("touchMove." + this._uid, this._onMouseMove), this.dispatch.mouseDown(t), this.dispatch.dragStart(t)
		    }, t.prototype._onMouseUp = function (t)
		    {
			return this._mouseDown = !1,
			    M.WindowEvents.on("mouseUp." + this._uid, null), M.WindowEvents.on("mouseMove." + this._uid, null), M.WindowEvents.on("touchEnd." + this._uid, null), M.WindowEvents.on("touchCancel." + this._uid, null), M.WindowEvents.on("touchMove." + this._uid, null),
			    this.dispatch.mouseUp(t), this.dispatch.dragEnd(t)
		    }, t.prototype._onMouseWheel = function (t)
		    {
			return this.dispatch.mouseWheel(t)
		    }, t
	    }(), M.InertialMouse = function ()
	    {
		function t()
		{
		    this.reset()
		}
		return t.inertiaExtinction = .1, t.smoothingTimeout = 300,
		    t.inertiaMsecDelay = 30, t.prototype.get = function ()
		    {
			var t;
			return t = 1e3 / M.InertialMouse.inertiaMsecDelay, [this.x * t, this.y * t]
		    }, t.prototype.reset = function ()
		    {
			return this.xy = [0, 0], this
		    }, t.prototype.update = function (t)
		    {
			var e, n;
			return null != this.lastUpdate ? (e = (new Date).getTime() - this.lastUpdate.getTime(), t = t.map(function (t)
			    {
				return t / Math.max(e, 1)
			    }), n = Math.min(1, e / M.InertialMouse.smoothingTimeout), this.x = n * t[0] + (1 - n) * this.x,
			    this.y = n * t[1] + (1 - n) * this.y) : (this.x = t[0], this.y = t[1]), this.lastUpdate = new Date, this
		    }, t.prototype.damp = function ()
		    {
			return this.x *= 1 - M.InertialMouse.inertiaExtinction, this.y *= 1 - M.InertialMouse.inertiaExtinction, this
		    }, t
	    }(), M.Drag = function ()
	    {
		function t(t, e)
		{
		    var n;
		    this.el = t, this._stopInertia = bind(this._stopInertia, this), this._startInertia = bind(this._startInertia, this), this._onInertia = bind(this._onInertia, this), this._onDrag = bind(this._onDrag, this),
			this._onDragEnd = bind(this._onDragEnd, this), this._onDragStart = bind(this._onDragStart, this), this._getPageCoords = bind(this._getPageCoords, this), M.Util.defaults(this, e, this.defaults), this.el = M.Util.element(this.el),
			this._uid = M.Util.uniqueId("dragger-"), this._inertiaRunning = !1, this._dragState = {
			    dragging: !1,
			    origin: null,
			    last: null,
			    inertia: new M.InertialMouse
			}, this.dispatch = M.Events.dispatch("drag", "dragStart", "dragEnd", "dragEndInertia"),
			this.on = this.dispatch.on, (n = new M.MouseEvents(this.el)).on("dragStart." + this._uid, this._onDragStart), n.on("dragEnd." + this._uid, this._onDragEnd), n.on("drag." + this._uid, this._onDrag)
		}
		return t.prototype.defaults = {
			inertia: !1
		    },
		    t.prototype._getPageCoords = function (t)
		    {
			var e, n;
			return (null != (e = t.touches) ? e.length : void 0) > 0 ? [t.touches[0].pageX, t.touches[0].pageY] : (null != (n = t.changedTouches) ? n.length : void 0) > 0 ? [t.changedTouches[0].pageX, t.changedTouches[0].pageY] : [t.pageX, t.pageY]
		    },
		    t.prototype._onDragStart = function (t)
		    {
			return this._stopInertia(), this._dragState.dragging = !0, this._dragState.origin = this._getPageCoords(t), this._dragState.last = this._getPageCoords(t), this.dispatch.dragStart(t)
		    },
		    t.prototype._onDragEnd = function (t)
		    {
			var e, n;
			return this._dragState.dragging = !1, this.inertia && (e = {
			    offset: [(n = this._getPageCoords(t))[0] - this._dragState.origin[0], n[1] - this._dragState.origin[1]],
			    offsetRelative: [n[0] - this._dragState.last[0], n[1] - this._dragState.last[1]]
			}, this._dragState.inertia.update(e.offsetRelative), this._startInertia()), this.dispatch.dragEnd(t)
		    }, t.prototype._onDrag = function (t)
		    {
			var e, n;
			return e = {
				offset: [(n = this._getPageCoords(t))[0] - this._dragState.origin[0], n[1] - this._dragState.origin[1]],
				offsetRelative: [n[0] - this._dragState.last[0], n[1] - this._dragState.last[1]]
			    }, this.dispatch.drag(e),
			    this.inertia && this._dragState.inertia.update(e.offsetRelative), this._dragState.last = n
		    }, t.prototype._onInertia = function ()
		    {
			var t;
			if (this._inertiaRunning) return t = this._dragState.inertia.damp().get(),
			    Math.abs(t[0]) < 1 && Math.abs(t[1]) < 1 ? (this._stopInertia(), void this.dispatch.dragEndInertia()) : (this.dispatch.drag(
			    {
				offset: [this._dragState.last[0] - this._dragState.origin[0], this._dragState.last[0] - this._dragState.origin[1]],
				offsetRelative: t
			    }), this._dragState.last = [this._dragState.last[0] + t[0], this._dragState.last[1] + t[1]], this._startInertia())
		    }, t.prototype._startInertia = function ()
		    {
			return this._inertiaRunning = !0, setTimeout(this._onInertia, M.InertialMouse.inertiaMsecDelay)
		    },
		    t.prototype._stopInertia = function ()
		    {
			return this._dragState.inertia.reset(), this._inertiaRunning = !1
		    }, t
	    }(), M.Zoom = function ()
	    {
		function t(t, e)
		{
		    this.el = t, this._onMouseWheel = bind(this._onMouseWheel, this), M.Util.defaults(this, e, this.defaults),
			this.el = M.Util.element(this.el), this._uid = M.Util.uniqueId("zoomer-"), this.dispatch = M.Events.dispatch("zoom"), this.on = this.dispatch.on, new M.MouseEvents(this.el).on("mouseWheel." + this._uid, this._onMouseWheel)
		}
		return t.prototype.defaults = {
		    speed: .25
		}, t.prototype._onMouseWheel = function (t)
		{
		    var e, n, P;
		    return t.preventDefault(), e = t.wheelDelta / Math.abs(t.wheelDelta), P = Math.abs(t.wheelDelta) / 120 * this.speed, n = Math.pow(2, e * P), this.dispatch.zoom(
		    {
			zoom: n
		    })
		}, t
	    }(), M.Surface = function ()
	    {
		function t(t, e)
		{
		    this.points = t, this.painter = null != e ? e : M.Painters.path, this.id = "s" + M.Util.uniqueId()
		}
		return t.prototype.cullBackfaces = !0, t.prototype.fillMaterial = new M.Material(M.C.gray), t.prototype.strokeMaterial = null,
		    t.prototype.fill = function (t)
		    {
			return this.fillMaterial = M.Material.create(t), this
		    }, t.prototype.stroke = function (t)
		    {
			return this.strokeMaterial = M.Material.create(t), this
		    }, t
	    }(), M.Shape = function (t)
	    {
		function e(t, n)
		{
		    this.type = t, this.surfaces = n,
			e.__super__.constructor.call(this)
		}
		return extend(e, t), e.prototype.eachSurface = function (t)
		    {
			return this.surfaces.forEach(t), this
		    }, e.prototype.fill = function (t)
		    {
			return this.eachSurface(function (e)
			{
			    return e.fill(t)
			}), this
		    },
		    e.prototype.stroke = function (t)
		    {
			return this.eachSurface(function (e)
			{
			    return e.stroke(t)
			}), this
		    }, e
	    }(M.Transformable), M.Model = function (t)
	    {
		function e()
		{
		    e.__super__.constructor.call(this), this.children = [], this.lights = []
		}
		return extend(e, t),
		    e.prototype.add = function ()
		    {
			var t, e, n, P;
			for (P = 0, n = (e = 1 <= arguments.length ? slice.call(arguments, 0) : []).length; P < n; P++)(t = e[P]) instanceof M.Shape || t instanceof M.Model ? this.children.push(t) : t instanceof M.Light && this.lights.push(t);
			return this
		    }, e.prototype.remove = function ()
		    {
			var t, e, n, P, r, i;
			for (i = [], r = 0, P = (e = 1 <= arguments.length ? slice.call(arguments, 0) : []).length; r < P; r++)
			{
			    for (t = e[r];
				(n = this.children.indexOf(t)) >= 0;) this.children.splice(n, 1);
			    i.push(function ()
			    {
				var e;
				for (e = [];
				    (n = this.lights.indexOf(t)) >= 0;) e.push(this.lights.splice(n, 1));
				return e
			    }.call(this))
			}
			return i
		    }, e.prototype.append = function ()
		    {
			var t;
			return t = new M.Model, this.add(t), t
		    }, e.prototype.eachShape = function (t)
		    {
			var e, n, P, r, i;
			for (i = [], P = 0,
			    n = (r = this.children).length; P < n; P++)(e = r[P]) instanceof M.Shape && t.call(this, e), e instanceof M.Model ? i.push(e.eachShape(t)) : i.push(void 0);
			return i
		    }, e.prototype.eachRenderable = function (t, e)
		    {
			return this._eachRenderable(t, e, [], this.m)
		    },
		    e.prototype._eachRenderable = function (t, e, n, P)
		    {
			var r, i, s, a, o, u, h, c, p;
			for (this.lights.length > 0 && (n = n.slice()), o = 0, i = (u = this.lights).length; o < i; o++)(a = u[o]).enabled && n.push(t.call(this, a, a.m.copy().multiply(P)));
			for (c = [], p = 0,
			    s = (h = this.children).length; p < s; p++)(r = h[p]) instanceof M.Shape && e.call(this, r, n, r.m.copy().multiply(P)), r instanceof M.Model ? c.push(r._eachRenderable(t, e, n, r.m.copy().multiply(P))) : c.push(void 0);
			return c
		    }, e
	    }(M.Transformable), M.Models = {
		default: function ()
		{
		    var t;
		    return (t = new M.Model).add(M.Lights.directional(
			{
			    normal: M.P(-1, 1, 1).normalize(),
			    color: M.Colors.hsl(.1, .3, .7),
			    intensity: .004
			})), t.add(M.Lights.directional(
			{
			    normal: M.P(1, 1, -1).normalize(),
			    intensity: .003
			})),
			t.add(M.Lights.ambient(
			{
			    intensity: .0015
			})), t
		}
	    }, "undefined" != typeof window && null !== window && (T = null != (x = null != (_ = null != (b = window.requestAnimationFrame) ? b : window.mozRequestAnimationFrame) ? _ : window.webkitRequestAnimationFrame) ? x : window.msRequestAnimationFrame), M.Animator = function ()
	    {
		function t()
		{
		    this.frame = bind(this.frame, this), this.dispatch = M.Events.dispatch("beforeFrame", "afterFrame", "frame"), this.on = this.dispatch.on, this.timestamp = 0, this._running = !1, this.frameDelay = null
		}
		return t.prototype.start = function ()
		    {
			return this._running = !0, null != this.frameDelay && (this._lastTime = (new Date).valueOf(), this._delayCompensation = 0), this.animateFrame(), this
		    }, t.prototype.stop = function ()
		    {
			return this._running = !1, this
		    }, t.prototype.animateFrame = function ()
		    {
			var t, e, n;
			return null != T && null == this.frameDelay ? T(this.frame) : (t = (new Date).valueOf() - this._lastTime, this._lastTime += t, this._delayCompensation += t, e = null != (n = this.frameDelay) ? n : 30, setTimeout(this.frame, e - this._delayCompensation)), this
		    },
		    t.prototype.frame = function (t)
		    {
			var e, n;
			if (this._running) return this._timestamp = null != t ? t : this._timestamp + (null != (n = this._msecDelay) ? n : 30), e = null != this._lastTimestamp ? this._timestamp - this._lastTimestamp : this._timestamp,
			    this.dispatch.beforeFrame(this._timestamp, e), this.dispatch.frame(this._timestamp, e), this.dispatch.afterFrame(this._timestamp, e), this._lastTimestamp = this._timestamp, this.animateFrame(), this
		    }, t.prototype.onBefore = function (t)
		    {
			return this.on("beforeFrame." + M.Util.uniqueId("animator-"), t), this
		    }, t.prototype.onAfter = function (t)
		    {
			return this.on("afterFrame." + M.Util.uniqueId("animator-"), t), this
		    }, t.prototype.onFrame = function (t)
		    {
			return this.on("frame." + M.Util.uniqueId("animator-"), t), this
		    }, t
	    }(), M.RenderAnimator = function (t)
	    {
		function e(t)
		{
		    e.__super__.constructor.apply(this, arguments), this.onFrame(t.render)
		}
		return extend(e, t), e
	    }(M.Animator), M.Transition = function ()
	    {
		function t(t)
		{
		    null == t && (t = {}), M.Util.defaults(this, t, this.defaults)
		}
		return t.prototype.defaults = {
		    duration: 100
		}, t.prototype.update = function (t)
		{
		    return null == this.t && (this.firstFrame(), this.startT = t), this.t = t,
			this.tFrac = (this.t - this.startT) / this.duration, this.frame(), !(this.tFrac >= 1) || (this.lastFrame(), !1)
		}, t.prototype.firstFrame = function () {}, t.prototype.frame = function () {}, t.prototype.lastFrame = function () {}, t
	    }(), M.TransitionAnimator = function (t)
	    {
		function e()
		{
		    this.update = bind(this.update, this), e.__super__.constructor.apply(this, arguments), this.queue = [], this.transitions = [], this.onFrame(this.update)
		}
		return extend(e, t), e.prototype.add = function (t)
		    {
			return this.transitions.push(t)
		    },
		    e.prototype.keyframe = function ()
		    {
			return this.queue.push(this.transitions), this.transitions = []
		    }, e.prototype.update = function (t)
		    {
			var e;
			if (this.queue.length) return (e = (e = this.queue.shift()).filter(function (e)
			{
			    return e.update(t)
			})).length ? this.queue.unshift(e) : void 0
		    }, e
	    }(M.Animator), g = [
		[0, 2, 1],
		[0, 1, 3],
		[3, 2, 0],
		[1, 2, 3]
	    ], n = [
		[0, 1, 3, 2],
		[5, 4, 6, 7],
		[1, 0, 4, 5],
		[2, 3, 7, 6],
		[3, 1, 5, 7],
		[0, 2, 6, 4]
	    ], m = [
		[1, 0, 2, 3],
		[0, 1, 4],
		[2, 0, 4],
		[3, 2, 4],
		[1, 3, 4]
	    ], i = Math.sqrt(3) / 2, c = .5257311121191336,
	    p = .8506508083520399, h = [M.P(-c, 0, -p), M.P(c, 0, -p), M.P(-c, 0, p), M.P(c, 0, p), M.P(0, p, -c), M.P(0, p, c), M.P(0, -p, -c), M.P(0, -p, c), M.P(p, c, 0), M.P(-p, c, 0), M.P(p, -c, 0), M.P(-p, -c, 0)],
	    u = [
		[0, 4, 1],
		[0, 9, 4],
		[9, 5, 4],
		[4, 5, 8],
		[4, 8, 1],
		[8, 10, 1],
		[8, 3, 10],
		[5, 3, 8],
		[5, 2, 3],
		[2, 7, 3],
		[7, 10, 3],
		[7, 6, 10],
		[7, 11, 6],
		[11, 0, 6],
		[0, 1, 6],
		[6, 1, 10],
		[9, 0, 11],
		[9, 11, 2],
		[9, 2, 5],
		[7, 2, 11]
	    ],

		
	    
	    Zxxxx = [M.P(.0, .0, .0), M.P(.1, .0, .0), M.P(.0, .1, .0), M.P(.1, .1, .0),  
		  M.P(.0, .0, .1), M.P(.1, .0, .1), M.P(.0, .1, .1), M.P(.1, .1, .1),
		
		  M.P(.0, .0, .1), M.P(.1, .0, .1), M.P(.0, .1, .1), M.P(.1, .1, .1),
		  M.P(.0, .0, .2), M.P(.1, .0, .2), M.P(.0, .1, .2), M.P(.1, .1, .2),
		
		  M.P(.0, .0, .2), M.P(.1, .0, .2), M.P(.0, .1, .2), M.P(.1, .1, .2),
		  M.P(.0, .0, .3), M.P(.1, .0, .3), M.P(.0, .1, .3), M.P(.1, .1, .3),
		
		  M.P(.1, .0, .0), M.P(.2, .0, .0), M.P(.1, .1, .0), M.P(.2, .1, .0),
		  M.P(.1, .0, .1), M.P(.2, .0, .1), M.P(.1, .1, .1), M.P(.2, .1, .1),
		
		  M.P(0, .0, .0), M.P(-.1, .0, .0), M.P(.0, .1, .0), M.P(-.1, .1, .0),
		  M.P(0, .0, .1), M.P(-.1, .0, .1), M.P(.0, .1, .1), M.P(-.1, .1, .1),	
		
		  M.P(.0, .1, .0), M.P(.1, .1, .0), M.P(.0, .2, .0), M.P(.1, .2, .0),  
		  M.P(.0, .1, .1), M.P(.1, .1, .1), M.P(.0, .2, .1), M.P(.1, .2, .1),
		
//				  M.P(.0, .1, .0), M.P(.1, .1, .0), M.P(.0, .2, .0), M.P(.1, .2, .0),  
//				  M.P(.0, .1, .3), M.P(.1, .1, .3), M.P(.0, .2, .3), M.P(.1, .2, .3),
		
//				  M.P(.0, .1, .0), M.P(.1, .1, .0), M.P(.0, .2, .0), M.P(.1, .2, .0),  
//				  M.P(.0, .1, .4), M.P(.1, .1, .4), M.P(.0, .2, .4), M.P(.1, .2, .4),
		
			    
				     
	    ],
	    
	    
	    Zzzzzzz = [
		[0, 1, 3, 2],	[0, 2, 3, 1],

		[1, 3, 7, 5],	[1, 5, 7, 3],
		
		[2, 3, 7, 6],	[2, 6, 7, 3],
		
		[0, 1, 5, 4],	[0, 4, 5, 1],
		
		[0, 2, 6, 4],	[0, 4, 6, 2],
		
		[4, 5, 7, 6],	[4, 6, 7, 5],
		
		
		[8, 9, 11, 10],		[8, 10, 11, 9],

		[9, 11, 15, 13],	[9, 13, 15, 11],
		
		[10, 11, 15, 14],	[10, 14, 15, 11],
		
		[8, 9, 13, 12],		[8, 12, 13, 9],
		
		[8, 10, 14, 12],	[8, 12, 14, 10],
		
		[12, 13, 15, 14],	[12, 14, 15, 13],
		
		
		[16, 17, 19, 18],	[16, 18, 19, 17],

		[17, 19, 23, 21],	[17, 21, 23, 19],
		
		[18, 19, 23, 22],	[18, 22, 23, 19],
		
		[16, 17, 21, 20],	[16, 20, 21, 17],
		
		[16, 18, 22, 20],	[16, 20, 22, 18],
		
		[20, 21, 23, 22],	[20, 22, 23, 21],
		
		
		[24, 25, 27, 26],	[24, 26, 27, 25],

		[25, 27, 31, 29],	[25, 29, 31, 27],
		
		[26, 27, 31, 30],	[26, 30, 31, 27],
		
		[24, 25, 29, 28],	[24, 28, 29, 25],
		
		[24, 26, 30, 28],	[24, 28, 30, 26],
		
		[28, 29, 31, 30],	[28, 30, 31, 29],
		
		
		[32, 33, 35, 34],	[32, 34, 35, 33],

		[33, 35, 39, 37],	[33, 37, 39, 35],
		
		[34, 35, 39, 38],	[34, 38, 39, 35],
		
		[32, 33, 37, 36],	[32, 36, 37, 33],
		
		[32, 34, 38, 36],	[32, 36, 38, 34],
		
		[36, 37, 39, 38],	[36, 38, 39, 37],
		
		
		[40, 41, 43, 42],	[40, 42, 43, 41],

		[41, 43, 47, 45],	[41, 45, 47, 43],
		
		[42, 43, 47, 46],	[42, 46, 47, 43],
		
		[40, 41, 45, 44],	[40, 44, 45, 41],
		
		[40, 42, 46, 44],	[40, 44, 46, 42],
		
		[44, 45, 47, 46],	[44, 46, 47, 45],
		
	    ],


Zx = [
    M.P(0, 0, 0),   M.P(1, 0, 0),   M.P(0, 1, 0),   M.P(1, 1, 0),
    M.P(0, 0, 2),   M.P(1, 0, 2),   M.P(0, 1, 2),   M.P(1, 1, 2),

    M.P(1, 0, 0),   M.P(2, 0, 0),   M.P(1, 1, 0),   M.P(2, 1, 0),
    M.P(1, 0, 3),   M.P(2, 0, 3),   M.P(1, 1, 3),   M.P(2, 1, 3),

    M.P(1, 1, 0),   M.P(2, 1, 0),   M.P(1, 2, 0),   M.P(2, 2, 0),
    M.P(1, 1, 2),   M.P(2, 1, 2),   M.P(1, 2, 2),   M.P(2, 2, 2),

    M.P(2, 0, 0),   M.P(3, 0, 0),   M.P(2, 1, 0),   M.P(3, 1, 0),
    M.P(2, 0, 4),   M.P(3, 0, 4),   M.P(2, 1, 4),   M.P(3, 1, 4),

    M.P(2, 1, 0),   M.P(3, 1, 0),   M.P(2, 2, 0),   M.P(3, 2, 0),
    M.P(2, 1, 1),   M.P(3, 1, 1),   M.P(2, 2, 1),   M.P(3, 2, 1),

    M.P(2, 2, 0),   M.P(3, 2, 0),   M.P(2, 3, 0),   M.P(3, 3, 0),
    M.P(2, 2, 3),   M.P(3, 2, 3),   M.P(2, 3, 3),   M.P(3, 3, 3),
],


Zz = 	[
[0, 1, 3, 2], 
    [0, 2, 3, 1],
[4, 5, 7, 6], 
    [4, 6, 7, 5],
[0, 1, 5, 4], 
    [0, 4, 5, 1],
[0, 2, 6, 4], 
    [0, 4, 6, 2],
[2, 3, 7, 6], 
    [2, 6, 7, 3],
[1, 3, 7, 5], 
    [1, 5, 7, 3],

[8, 9, 11, 10],   [8, 10, 11, 9],
[12, 13, 15, 14], [12, 14, 15, 13],
[8, 9, 13, 12],   [8, 12, 13, 9],
[8, 10, 14, 12],  [8, 12, 14, 10],
[10, 11, 15, 14], [10, 14, 15, 11],
[9, 11, 15, 13],  [9, 13, 15, 11],

[16, 17, 19, 18], [16, 18, 19, 17],
[20, 21, 23, 22], [20, 22, 23, 21],
[16, 17, 21, 20], [16, 20, 21, 17],
[16, 18, 22, 20], [16, 20, 22, 18],
[18, 19, 23, 22], [18, 22, 23, 19],
[17, 19, 23, 21], [17, 21, 23, 19],

[24, 25, 27, 26], [24, 26, 27, 25],
[28, 29, 31, 30], [28, 30, 31, 29],
[24, 25, 29, 28], [24, 28, 29, 25],
[24, 26, 30, 28], [24, 28, 30, 26],
[26, 27, 31, 30], [26, 30, 31, 27],
[25, 27, 31, 29], [25, 29, 31, 27],

[32, 33, 35, 34], [32, 34, 35, 33],
[36, 37, 39, 38], [36, 38, 39, 37],
[32, 33, 37, 36], [32, 36, 37, 33],
[32, 34, 38, 36], [32, 36, 38, 34],
[34, 35, 39, 38], [34, 38, 39, 35],
[33, 35, 39, 37], [33, 37, 39, 35],

[40, 41, 43, 42], [40, 42, 43, 41],
[44, 45, 47, 46], [44, 46, 47, 45],
[40, 41, 45, 44], [40, 44, 45, 41],
[40, 42, 46, 44], [40, 44, 46, 42],
[42, 43, 47, 46], [42, 46, 47, 43],
[41, 43, 47, 45], [41, 45, 47, 43],
    ],	
/*	
[0, 1, 3, 2], [0, 2, 3, 1],
[4, 5, 7, 6], [4, 6, 7, 5],

[0, 2, 6, 4], [0, 4, 6, 2],
[0, 1, 5, 4], [0, 4, 5, 1],

[2, 3, 7, 6], [2, 6, 7, 3],
[1, 3, 7, 5], [1, 5, 7, 3],

    
[8, 9, 11, 10], [8, 10, 11, 9],
[12, 13, 15, 14], [12, 14, 15, 13],

[8, 10, 14, 12], [8, 12, 14, 10],
[9, 11, 15, 13], [9, 13, 15, 11],
    
[8, 9, 13, 12], [8, 12, 13, 9],
[10, 11, 15, 14], [10, 14, 15, 11],
    
    
[16, 17, 19, 18], [16, 18, 19, 17],
[20, 21, 23, 22], [20, 22, 23, 21],
    
[16, 18, 22, 20], [16, 20, 22, 18],
[17, 19, 23, 21], [17, 21, 23, 19],
    
[16, 17, 21, 20], [16, 20, 21, 17],
[18, 19, 23, 22], [18, 22, 23, 19],	
    
    
    [24, 25, 27, 26], [24, 26, 27, 25],
    [28, 29, 31, 30], [28, 25, 27, 26],
    
    [24, 26, 30, 28], [24, 28, 30, 26],
    [25, 27, 31, 29], [25, 29, 31, 27],
    
    [24, 25, 29, 28], [24, 28, 29, 25], 
    [26, 27, 31, 30], [26, 30, 31, 27],
    


[32, 33, 35, 34], [32, 34, 35, 33],
[36, 37, 39, 38], [36, 38, 39, 37],
    
[32, 34, 38, 36], [32, 36, 38, 34],
[33, 35, 39, 37], [33, 37, 39, 35],
    
[32, 33, 37, 36], [32, 36, 37, 33],
[34, 35, 39, 38], [34, 38, 39, 35],
	
    
],
/*
[4, 6, 7, 3], [4, 3, 7, 6],
[6, 10, 11, 7], [6, 7, 11, 10],
    

    
    ],	
    
/*	

[4, 8, 9, 5], [4, 5, 9, 8],
[5, 3, 7, 9], [5, 9, 7, 3],
[3, 4, 6, 5], [3, 5, 6, 4],
[7, 8, 10, 9], [7, 9, 10, 8],
[3, 7, 8, 4], [3, 4, 8, 7],
[3, 7, 9, 5], [3, 5, 9, 7],
[5, 9, 10, 6], [5, 6, 10, 9],
[6, 4, 8, 10], [6, 10, 8, 4],
[4, 5, 7, 6], [4, 6, 7, 5],
[8, 9, 11, 10], [8, 10, 11, 9],
[4, 8, 9, 5], [4, 5, 9, 8],
[4, 8, 10, 6], [4, 6, 10, 8],
[6, 10, 11, 7], [6, 7, 11, 10],
[7, 5, 9, 11], [7, 11, 9, 5],
[5, 6, 8, 7], [5, 7, 8, 6],
[9, 10, 12, 11], [9, 11, 12, 10],
[5, 9, 10, 6], [5, 6, 10, 9],
[5, 9, 11, 7], [5, 7, 11, 9],
[7, 11, 12, 8], [7, 8, 12, 11],
[8, 6, 10, 12], [8, 12, 10, 6],
[6, 7, 9, 8], [6, 8, 9, 7],
[10, 11, 13, 12], [10, 12, 13, 11],
    ],	
    
/*	
[0, 1, 3, 2], [0, 2, 3, 1],
[4, 5, 7, 6], [4, 6, 7, 5],

[0, 4, 5, 1], [0, 1, 5, 4],
[0, 4, 6, 2], [0, 2, 6, 4],


[2, 6, 7, 3], [2, 3, 7, 6],
[3, 1, 5, 7], [3, 7, 5, 1],
    


[8, 9, 11, 10], [8, 10, 11, 9],
[12, 13, 15, 14], [12, 14, 15, 13],	

    
[8, 12, 13, 9], [8, 9, 13, 12],
    [8, 12,14,10], [8,10,14,12],
    
],
/*	
    
[10, 12, 15, 11], [10,11,15,12],
    [11,9,13,15], [11,15,13,9],	

// [0, 1, 3, 2], [0, 2, 3, 1],
// [4, 5, 7, 6], [4, 6, 7, 5],
// [0, 4, 5, 1], [0, 1, 5, 4],
// [0, 4, 6, 2], [0, 2, 6, 4],
// [2, 6, 7, 3], [2, 3, 7, 6],
// [3, 1, 5, 7], [3, 7, 5, 1],
[3, 4, 6, 5], [3, 5, 6, 4],
[7, 8, 10, 9], [7, 9, 10, 8],
[3, 7, 8, 4], [3, 4, 8, 7],
[3, 7, 9, 5], [3, 5, 9, 7],
[5, 9, 10, 6], [5, 6, 10, 9],
[6, 4, 8, 10], [6, 10, 8, 4],
[4, 5, 7, 6], [4, 6, 7, 5],
[8, 9, 11, 10], [8, 10, 11, 9],
[4, 8, 9, 5], [4, 5, 9, 8],
[4, 8, 10, 6], [4, 6, 10, 8],
[6, 10, 11, 7], [6, 7, 11, 10],
[7, 5, 9, 11], [7, 11, 9, 5],
[6, 7, 9, 8], [6, 8, 9, 7],
[10, 11, 13, 12], [10, 12, 13, 11],
[6, 10, 11, 7], [6, 7, 11, 10],
[6, 10, 12, 8], [6, 8, 12, 10],
[8, 12, 13, 9], [8, 9, 13, 12],
[9, 7, 11, 13], [9, 13, 11, 7],
[7, 8, 10, 9], [7, 9, 10, 8],
[11, 12, 14, 13], [11, 13, 14, 12],
[7, 11, 12, 8], [7, 8, 12, 11],
[7, 11, 13, 9], [7, 9, 13, 11],
[9, 13, 14, 10], [9, 10, 14, 13],
[10, 8, 12, 14], [10, 14, 12, 8],
[8, 9, 11, 10], [8, 10, 11, 9],
[12, 13, 15, 14], [12, 14, 15, 13],
[8, 12, 13, 9], [8, 9, 13, 12],
[8, 12, 14, 10], [8, 10, 14, 12],
[10, 14, 15, 11], [10, 11, 15, 14],
[11, 9, 13, 15], [11, 15, 13, 9],

	],
*/			

	    Zxx = [M.P(.0, .0, .0), M.P(.4, .0, .0), M.P(.0, .4, .0), M.P(.4, .4, .0),  
    
	      M.P(.0, .0, .4), M.P(.4, .0, .4), M.P(.0, .4, .4), M.P(.4, .4, .4),
	      M.P(.0, .0, .8), M.P(.4, .0, .8), M.P(.0, .4, .8), M.P(.4, .4, .8),
		
	      M.P(.0, .0, 1.2), M.P(.4, .0, 1.2), M.P(.0, .4, 1.2), M.P(.4, .4, 1.2),					 
	    ],
	
	    
	    Zzz = [
		[0, 1, 3, 2],	[0, 2, 3, 1],
		[1, 3, 7, 5],	[1, 5, 7, 3],				
		[2, 3, 7, 6],	[2, 6, 7, 3],
		[0, 1, 5, 4],	[0, 4, 5, 1],
		[0, 2, 6, 4],	[0, 4, 6, 2],
		[4, 5, 7, 6],	[4, 6, 7, 5],
		
		
		[4, 5, 7, 6],	[4, 6, 7, 5],
		[5, 7, 11, 9],	[5, 9, 11, 7],
		[6, 7, 11, 10],	[6, 10, 11, 7],
		[4, 5, 9, 8],		[4, 8, 9, 5],
		[4, 6, 10, 8],	[4, 8, 10, 6],
		[8, 9, 11, 10],	[8, 10, 11, 9],
		
		
		[8, 9, 11, 10],	[8, 10, 11, 9],
		[9, 11, 15, 13],	[9, 13, 15, 11],
		[10, 11, 15, 14],	[10, 14, 15, 11],
		[8, 9, 13, 12],	[8, 12, 13, 9],
		[8, 10, 14, 12],	[8, 12, 14, 10],
		[12, 13, 15, 14],	[12, 14, 15, 13],
	    ],					
	    
	    
	    
	    tmp1 = [M.P(.0, .0, .0), M.P(.0, .0, .3), M.P(.3, .0, .3), M.P(.3, .0, .0), M.P(.0, .3, .0), M.P(.0, .3, .3), M.P(.3, .3, .3), M.P(.3, .3, .0)], 
	    tmp2 = [
		[3, 2, 1, 0],
		[3, 0, 1, 2],
		
		[0, 3, 7, 4],
		[0, 4, 7, 3],
		
		[2, 3, 7, 6],
		[2, 6, 7, 3],
		
		[5, 6, 2, 1],
		[5, 1, 2, 6],
		
		[1, 0, 4, 5],
		[1, 5, 4, 0],
		
		[6, 7, 4, 5],
		[6, 5, 4, 7]				
		
	    ],			
	    
	    
		    
	    Lt = [M.P(-.729665, .670121, .319155), M.P(-.655235, -.29213, -.754096), M.P(-.093922, -.607123, .537818), M.P(.702196, .595691, .485187), M.P(.776626, -.36656, -.588064)], Dt = [
		[1, 4, 2],
		[0, 1, 2],
		[3, 0, 2],
		[4, 3, 2],
		[4, 1, 0, 3]
	    ],




	    rr = [M.P(-.748928, .557858, -.030371), M.P(-.638635, .125804, -.670329), M.P(-.593696, .259282, .67329), M.P(-.427424, .876636, -.665507), M.P(-.373109, -.604827, -.606627), M.P(-.32817, -.471348, .736992), M.P(-.217876, -.903403, .097033), M.P(-.141658, 1.042101, .041134), M.P(-.021021, .094954, 1.176701), M.P(.013575, .743525, .744795), M.P(.036802, .343022, -.994341), M.P(.267732, -1.036179, -.498733), M.P(.302328, -.387609, -.93064), M.P(.443205, -.438661, .847867), M.P(.499183, .610749, .149029), M.P(.553499, -.870715, .207908), M.P(.609478, .178694, -.490931), M.P(.76471, -.119883, .212731)],
	    Pr = [
		[12, 11, 4],
		[11, 6, 4],
		[6, 11, 15],
		[13, 15, 17],
		[5, 13, 8],
		[2, 5, 8],
		[2, 8, 9],
		[7, 9, 14],
		[16, 14, 17],
		[3, 0, 7],
		[3, 1, 0],
		[1, 3, 10],
		[12, 10, 16],
		[6, 15, 13, 5],
		[2, 9, 7, 0],
		[12, 4, 1, 10],
		[11, 12, 16, 17, 15],
		[8, 13, 17, 14, 9],
		[16, 10, 3, 7, 14],
		[1, 4, 6, 5, 2, 0]
	    ], M.Shapes = {
		cube: function ()
		{
		    var t;
		    return t = [M.P(-1, -1, -1), M.P(-1, -1, 1), M.P(-1, 1, -1), M.P(-1, 1, 1), M.P(1, -1, -1), M.P(1, -1, 1), M.P(1, 1, -1), M.P(1, 1, 1)], new M.Shape("cube", M.Shapes.mapPointsToSurfaces(t, n))
		},
		unitcube: function ()
		{
		    var t;
		    return t = [M.P(0, 0, 0), M.P(0, 0, 1), M.P(0, 1, 0), M.P(0, 1, 1), M.P(1, 0, 0), M.P(1, 0, 1), M.P(1, 1, 0), M.P(1, 1, 1)], new M.Shape("unitcube", M.Shapes.mapPointsToSurfaces(t, n))
		},
		rectangle: function (t, e)
		{
		    var P, r;
		    return r = [(P = function (n, P, r)
			{
			    return M.P(n(t.x, e.x), P(t.y, e.y), r(t.z, e.z))
			})(Math.min, Math.min, Math.min), P(Math.min, Math.min, Math.max), P(Math.min, Math.max, Math.min), P(Math.min, Math.max, Math.max), P(Math.max, Math.min, Math.min), P(Math.max, Math.min, Math.max), P(Math.max, Math.max, Math.min), P(Math.max, Math.max, Math.max)],
			new M.Shape("rect", M.Shapes.mapPointsToSurfaces(r, n))
		},
		pyramid: function ()
		{
		    var t;
		    return t = [M.P(0, 0, 0), M.P(0, 0, 1), M.P(1, 0, 0), M.P(1, 0, 1), M.P(.5, 1, .5)], new M.Shape("pyramid", M.Shapes.mapPointsToSurfaces(t, m))
		},
		tetrahedron: function ()
		{
		    var t;
		    return t = [M.P(1, 1, 1), M.P(-1, -1, 1), M.P(-1, 1, -1), M.P(1, -1, -1)], new M.Shape("tetrahedron", M.Shapes.mapPointsToSurfaces(t, g))
		},
		icosahedron: function ()
		{
		    return new M.Shape("icosahedron", M.Shapes.mapPointsToSurfaces(h, u))
		},

		j1: function ()
		{
		    return new M.Shape("j1", M.Shapes.mapPointsToSurfaces(Lt, Dt))
		},

		j92: function ()
		{
		    return new M.Shape("j92", M.Shapes.mapPointsToSurfaces(rr, Pr))
		},
		j93: function ()
		{
		    return new M.Shape("j93", M.Shapes.mapPointsToSurfaces(Zx, Zz))
		},				
		sphere: function (t)
		{
		    var e, n, P;
		    for (null == t && (t = 2), P = u.map(function (t)
			{
			    return t.map(function (t)
			    {
				return h[t]
			    })
			}), e = 0, n = t; 0 <= n ? e < n : e > n; 0 <= n ? ++e : --e) P = M.Shapes._subdivideTriangles(P);
		    return new M.Shape("sphere", P.map(function (t)
		    {
			return new M.Surface(t.map(function (t)
			{
			    return t.copy()
			}))
		    }))
		},
		pipe: function (t, e, n, P)
		{
		    var r, i, s, a, o, u;
		    return null == n && (n = 1), null == P && (P = 8), r = e.copy().subtract(t), i = r.perpendicular().multiply(n), u = 2 * -Math.PI / P,
			a = M.Quaternion.pointAngle(r.copy().normalize(), u).toMatrix(), s = function ()
			{
			    o = [];
			    for (var t = 0; 0 <= P ? t < P : t > P; 0 <= P ? t++ : t--) o.push(t);
			    return o
			}.apply(this).map(function (e)
			{
			    var n;
			    return n = t.copy().add(i), i.transform(a), n
			}), M.Shapes.extrude(s, r)
		},
		patch: function (t, e)
		{
		    var n, P, r, s, a, o, u, h, c, p, l, f, d, m, S, y, g, w;
		    for (null == t && (t = 20), null == e && (e = 20), t = Math.round(t), e = Math.round(e), S = [], g = h = 0, l = t; 0 <= l ? h < l : h > l; g = 0 <= l ? ++h : --h)
		    {
			for (s = [], w = y = 0, f = e; 0 <= f ? y < f : y > f; w = 0 <= f ? ++y : --y)
			    for (n = 0,
				a = (d = [
				    [M.P(g, w), M.P(g + 1, w - .5), M.P(g + 1, w + .5)],
				    [M.P(g, w), M.P(g + 1, w + .5), M.P(g, w + 1)]
				]).length; n < a; n++)
			    {
				for (P = 0, o = (p = d[n]).length; P < o; P++)(c = p[P]).x *= i, c.y += g % 2 == 0 ? .5 : 0;
				s.push(p)
			    }
			if (g % 2 != 0)
			{
			    for (r = 0, u = (m = s[0]).length; r < u; r++)(c = m[r]).y += e;
			    s.push(s.shift())
			}
			S = S.concat(s)
		    }
		    return new M.Shape("patch", S.map(function (t)
		    {
			return new M.Surface(t)
		    }))
		},
		text: function (t, e)
		{
		    var n, P, r;
		    for (n in null == e && (e = {}), (P = new M.Surface(M.Affine.ORTHONORMAL_BASIS(), M.Painters.text)).text = t, e) r = e[n],
			P[n] = r;
		    return new M.Shape("text", [P])
		},
		extrude: function (t, e)
		{
		    var n, P, r, i, s, a, o, u;
		    for (u = [], P = new M.Surface(function ()
			{
			    var e, n, P;
			    for (P = [], n = 0, e = t.length; n < e; n++) a = t[n], P.push(a.copy());
			    return P
			}()), n = new M.Surface(function ()
			{
			    var n, P, r;
			    for (r = [], P = 0, n = t.length; P < n; P++) a = t[P], r.push(a.add(e));
			    return r
			}()), r = s = 1, o = t.length; 1 <= o ? s < o : s > o; r = 1 <= o ? ++s : --s) u.push(new M.Surface([P.points[r - 1].copy(), n.points[r - 1].copy(), n.points[r].copy(), P.points[r].copy()]));
		    return i = t.length,
			u.push(new M.Surface([P.points[i - 1].copy(), n.points[i - 1].copy(), n.points[0].copy(), P.points[0].copy()])), n.points.reverse(), u.push(P), u.push(n), new M.Shape("extrusion", u)
		},
		arrow: function (t, e, n, P, r)
		{
		    var i, s;
		    return null == t && (t = 1),
			null == e && (e = 1), null == n && (n = 1), null == P && (P = 1), null == r && (r = 0), i = n / 2, s = [M.P(0, 0, 0), M.P(P + r, 1, 0), M.P(P, i, 0), M.P(P + e, i, 0), M.P(P + e, -i, 0), M.P(P, -i, 0), M.P(P + r, -1, 0)], M.Shapes.extrude(s, M.P(0, 0, t))
		},
		path: function (t)
		{
		    return new M.Shape("path", [new M.Surface(t)])
		},
		custom: function (t)
		{
		    var e, n, P, r, i, s;
		    for (s = [], P = 0, n = (i = t.surfaces).length; P < n; P++) e = i[P], s.push(new M.Surface(function ()
		    {
			var t, n, P;
			for (n = [], P = 0, t = e.length; P < t; P++) r = e[P], n.push(M.P.apply(M, r));
			return n
		    }()));
		    return new M.Shape("custom", s)
		},
		mapPointsToSurfaces: function (t, e)
		{
		    var n, P, r, i, s, a;
		    for (a = [], i = 0, r = e.length; i < r; i++) P = e[i], s = function ()
			{
			    var e, r, i;
			    for (r = [], i = 0, e = P.length; i < e; i++) n = P[i], r.push(t[n].copy());
			    return r
			}(),
			a.push(new M.Surface(s));
		    return a
		},
		_subdivideTriangles: function (t)
		{
		    var e, n, P, r, i, s, a;
		    for (n = [], P = 0, e = t.length; P < e; P++) i = (r = t[P])[0].copy().add(r[1]).normalize(), s = r[1].copy().add(r[2]).normalize(), a = r[2].copy().add(r[0]).normalize(),
			n.push([r[0], i, a]), n.push([r[1], s, i]), n.push([r[2], a, s]), n.push([i, s, a]);
		    return n
		}
	    }, M.MocapModel = function ()
	    {
		function t(t, e, n)
		{
		    this.model = t, this.frames = e, this.frameDelay = n
		}
		return t.prototype.applyFrameTransforms = function (t)
		{
		    var e, n, P, r;
		    for (P = 0, n = (e = this.frames[t]).length; P < n; P++)(r = e[P]).shape.reset().transform(r.transform);
		    return (t + 1) % this.frames.length
		}, t
	    }(), M.MocapAnimator = function (t)
	    {
		function e(t)
		{
		    this.mocap = t, this.renderFrame = bind(this.renderFrame, this),
			e.__super__.constructor.apply(this, arguments), this.frameIndex = 0, this.frameDelay = this.mocap.frameDelay, this.onFrame(this.renderFrame)
		}
		return extend(e, t), e.prototype.renderFrame = function ()
		{
		    return this.frameIndex = this.mocap.applyFrameTransforms(this.frameIndex)
		}, e
	    }(M.Animator), M.Mocap = function ()
	    {
		function t(t)
		{
		    this.bvh = t
		}
		return t.DEFAULT_SHAPE_FACTORY = function (t, e)
		{
		    return M.Shapes.pipe(M.P(), e)
		}, t.parse = function (t)
		{
		    return new M.Mocap(M.BvhParser.parse(t))
		}, t.prototype.createMocapModel = function (t)
		{
		    var e, n, P, r;
		    return null == t && (t = M.Mocap.DEFAULT_SHAPE_FACTORY), P = new M.Model, n = [], this._attachJoint(P, this.bvh.root, n, t), e = this.bvh.motion.frames.map((r = this,
			function (t)
			{
			    return r._generateFrameTransforms(t, n)
			})), new M.MocapModel(P, e, 1e3 * this.bvh.motion.frameTime)
		}, t.prototype._generateFrameTransforms = function (t, e)
		{
		    var n, P;
		    return n = 0, e.map((P = this, function (e)
		    {
			var r, i;
			for (i = M.M(),
			    r = e.channels.length; r > 0;) r -= 1, P._applyChannelTransform(e.channels[r], i, t[n + r]);
			return n += e.channels.length, i.multiply(e.offset),
			{
			    shape: e.shape,
			    transform: i
			}
		    }))
		}, t.prototype._applyChannelTransform = function (t, e, n)
		{
		    switch (t)
		    {
			case "Xposition":
			    e.translate(n, 0, 0);
			    break;
			case "Yposition":
			    e.translate(0, n, 0);
			    break;
			case "Zposition":
			    e.translate(0, 0, n);
			    break;
			case "Xrotation":
			    e.rotx(n * Math.PI / 180);
			    break;
			case "Yrotation":
			    e.roty(n * Math.PI / 180);
			    break;
			case "Zrotation":
			    e.rotz(n * Math.PI / 180)
		    }
		    return e
		}, t.prototype._attachJoint = function (t, e, n, P)
		{
		    var r, i, s, a, o, u, h, c, p, l, f, d, m;
		    if (o = M.M().translate(null != (h = e.offset) ? h.x : void 0, null != (c = e.offset) ? c.y : void 0, null != (p = e.offset) ? p.z : void 0), t.transform(o), null != e.channels && n.push(
			{
			    shape: t,
			    offset: o,
			    channels: e.channels
			}), null != e.joints)
			for (i = t.append(), a = 0, s = (l = e.joints).length; a < s; a++) r = l[a], u = M.P(null != (f = r.offset) ? f.x : void 0, null != (d = r.offset) ? d.y : void 0, null != (m = r.offset) ? m.z : void 0), i.add(P(e, u)),
			    "JOINT" === r.type && this._attachJoint(i.append(), r, n, P)
		}, t
	    }(), M.ObjParser = function ()
	    {
		function t()
		{
		    var t;
		    this.vertices = [], this.faces = [], this.commands = {
			v: (t = this, function (e)
			{
			    return t.vertices.push(e.map(function (t)
			    {
				return parseFloat(t)
			    }))
			}),
			f: function (t)
			{
			    return function (e)
			    {
				return t.faces.push(e.map(function (t)
				{
				    return parseInt(t)
				}))
			    }
			}(this)
		    }
		}
		return t.prototype.parse = function (t)
		{
		    var e, n, P, r, i, s;
		    for (s = [], r = 0,
			P = (i = t.split(/[\r\n]+/)).length; r < P; r++)(n = i[r].trim().split(/[ ]+/)).length < 2 || (e = n.slice(0, 1)[0], n = n.slice(1),
			"#" !== e.charAt(0) && (null != this.commands[e] ? s.push(this.commands[e](n)) : console.log("OBJ Parser: Skipping unknown command '" + e + "'")));
		    return s
		}, t.prototype.mapFacePoints = function (t)
		{
		    return this.faces.map((e = this, function (n)
		    {
			var P;
			return P = n.map(function (t)
			{
			    return M.P.apply(M, e.vertices[t - 1])
			}), t.call(e, P)
		    }));
		    var e
		}, t
	    }(), M.Shapes.obj = function (t, e)
	    {
		var n;
		return null == e && (e = !0), (n = new M.ObjParser).parse(t), new M.Shape("obj", n.mapFacePoints(function (t)
		{
		    var n;
		    return (n = new M.Surface(t)).cullBackfaces = e, n
		}))
	    }, M.Projections = {
		perspectiveFov: function (t, e)
		{
		    var n;
		    return null == t && (t = 50), null == e && (e = 1), n = e * Math.tan(t * Math.PI / 360), M.Projections.perspective(-n, n, -n, n, e, 2 * e)
		},
		perspective: function (t, e, n, P, r, i)
		{
		    var s, a, o, u, h;
		    return null == t && (t = -1), null == e && (e = 1), null == n && (n = -1), null == P && (P = 1), null == r && (r = 1), null == i && (i = 100), h = 2 * r, s = e - t, a = P - n, o = i - r, (u = new Array(16))[0] = h / s, u[1] = 0, u[2] = (e + t) / s, u[3] = 0, u[4] = 0,
			u[5] = h / a, u[6] = (P + n) / a, u[7] = 0, u[8] = 0, u[9] = 0, u[10] = -(i + r) / o, u[11] = -i * h / o, u[12] = 0, u[13] = 0, u[14] = -1, u[15] = 0, M.M(u)
		},
		ortho: function (t, e, n, P, r, i)
		{
		    var s, a, o, u;
		    return null == t && (t = -1), null == e && (e = 1), null == n && (n = -1), null == P && (P = 1), null == r && (r = 1),
			null == i && (i = 100), 2 * r, s = e - t, a = P - n, o = i - r, (u = new Array(16))[0] = 2 / s, u[1] = 0, u[2] = 0, u[3] = (e + t) / s, u[4] = 0, u[5] = 2 / a, u[6] = 0, u[7] = -(P + n) / a, u[8] = 0, u[9] = 0, u[10] = -2 / o, u[11] = -(i + r) / o, u[12] = 0, u[13] = 0, u[14] = 0, u[15] = 1, M.M(u)
		}
	    }, M.Viewports = {
		center: function (t, e, n, P)
		{
		    return null == t && (t = 500), null == e && (e = 500), null == n && (n = 0), null == P && (P = 0),
		    {
			prescale: M.M().translate(-n, -P, -e).scale(1 / t, 1 / e, 1 / e),
			postscale: M.M().scale(t, -e, e).translate(n + t / 2, P + e / 2, e)
		    }
		},
		origin: function (t, e, n, P)
		{
		    return null == t && (t = 500), null == e && (e = 500), null == n && (n = 0), null == P && (P = 0),
		    {
			prescale: M.M().translate(-n, -P, -1).scale(1 / t, 1 / e, 1 / e),
			postscale: M.M().scale(t, -e, e).translate(n, P)
		    }
		}
	    }, M.Camera = function (t)
	    {
		function e(t)
		{
		    M.Util.defaults(this, t, this.defaults), e.__super__.constructor.apply(this, arguments)
		}
		return extend(e, t), e.prototype.defaults = {
		    projection: M.Projections.perspective()
		}, e
	    }(M.Transformable), M.Scene = function ()
	    {
		function t(t)
		{
		    this.flushCache = bind(this.flushCache, this), this.render = bind(this.render, this), M.Util.defaults(this, t, this.defaults()), this._renderModelCache = {}
		}
		return t.prototype.defaults = function ()
		{
		    return {
			model: new M.Model,
			camera: new M.Camera,
			viewport: M.Viewports.origin(1, 1),
			shader: M.Shaders.phong(),
			cullBackfaces: !0,
			fractionalPoints: !1,
			cache: !0
		    }
		}, t.prototype.render = function ()
		{
		    var t, e, n, P;
		    return t = this.camera.m.copy().multiply(this.viewport.prescale).multiply(this.camera.projection), n = this.viewport.postscale, e = [], this.model.eachRenderable(function (t, e)
		    {
			return new M.LightRenderModel(t, e)
		    }, (P = this, function (r, i, s)
		    {
			var a, o, u, h, c, p, l, f, d, m, S;
			for (d = [], u = 0, a = (h = r.surfaces).length; u < a; u++)
			    if (m = h[u], f = P._renderSurface(m, s, t, n), (!P.cullBackfaces || !m.cullBackfaces || f.projected.normal.z < 0) && f.inFrustrum)
			    {
				if (f.fill = null != (c = m.fillMaterial) ? c.render(i, P.shader, f.transformed) : void 0, f.stroke = null != (p = m.strokeMaterial) ? p.render(i, P.shader, f.transformed) : void 0, !0 !== P.fractionalPoints)
				    for (S = 0, o = (l = f.projected.points).length; S < o; S++) l[S].round();
				d.push(e.push(f))
			    }
			else d.push(void 0);
			return d
		    })), e.sort(function (t, e)
		    {
			return e.projected.barycenter.z - t.projected.barycenter.z
		    }), e
		}, t.prototype._renderSurface = function (t, e, n, P)
		{
		    var r;
		    return this.cache ? (null == (r = this._renderModelCache[t.id]) ? r = this._renderModelCache[t.id] = new M.RenderModel(t, e, n, P) : r.update(e, n, P), r) : new M.RenderModel(t, e, n, P)
		}, t.prototype.flushCache = function ()
		{
		    return this._renderModelCache = {}
		}, t
	    }(),
	    M.Grad = function ()
	    {
		function t(t, e, n)
		{
		    this.x = t, this.y = e, this.z = n
		}
		return t.prototype.dot = function (t, e, n)
		{
		    return this.x * t + this.y * e + this.z * n
		}, t
	    }(),
	    v = [new M.Grad(1, 1, 0), new M.Grad(-1, 1, 0), new M.Grad(1, -1, 0), new M.Grad(-1, -1, 0), new M.Grad(1, 0, 1), new M.Grad(-1, 0, 1), new M.Grad(1, 0, -1), new M.Grad(-1, 0, -1), new M.Grad(0, 1, 1), new M.Grad(0, -1, 1), new M.Grad(0, 1, -1), new M.Grad(0, -1, -1)],
	    y = [151, 160, 137, 91, 90, 15, 131, 13, 201, 95, 96, 53, 194, 233, 7, 225, 140, 36, 103, 30, 69, 142, 8, 99, 37, 240, 21, 10, 23, 190, 6, 148, 247, 120, 234, 75, 0, 26, 197, 62, 94, 252, 219, 203, 117, 35, 11, 32, 57, 177, 33, 88, 237, 149, 56, 87, 174, 20, 125, 136, 171, 168, 68, 175, 74, 165, 71, 134, 139, 48, 27, 166, 77, 146, 158, 231, 83, 111, 229, 122, 60, 211, 133, 230, 220, 105, 92, 41, 55, 46, 245, 40, 244, 102, 143, 54, 65, 25, 63, 161, 1, 216, 80, 73, 209, 76, 132, 187, 208, 89, 18, 169, 200, 196, 135, 130, 116, 188, 159, 86, 164, 100, 109, 198, 173, 186, 3, 64, 52, 217, 226, 250, 124, 123, 5, 202, 38, 147, 118, 126, 255, 82, 85, 212, 207, 206, 59, 227, 47, 16, 58, 17, 182, 189, 28, 42, 223, 183, 170, 213, 119, 248, 152, 2, 44, 154, 163, 70, 221, 153, 101, 155, 167, 43, 172, 9, 129, 22, 39, 253, 19, 98, 108, 110, 79, 113, 224, 232, 178, 185, 112, 104, 218, 246, 97, 228, 251, 34, 242, 193, 238, 210, 144, 12, 191, 179, 162, 241, 81, 51, 145, 235, 249, 14, 239, 107, 49, 192, 214, 31, 181, 199, 106, 157, 184, 84, 204, 176, 115, 121, 50, 45, 127, 4, 150, 254, 138, 236, 205, 93, 222, 114, 67, 29, 24, 72, 243, 141, 128, 195, 78, 66, 215, 61, 156, 180],
	    o = 1 / 6, M.Simplex3D = function ()
	    {
		function t(t)
		{
		    null == t && (t = 0), this.perm = new Array(512), this.gradP = new Array(512), this.seed(t)
		}
		return t.prototype.seed = function (t)
		{
		    var e, n, P, r;
		    for (t > 0 && t < 1 && (t *= 65536), (t = Math.floor(t)) < 256 && (t |= t << 8), P = [],
			e = n = 0; n < 256; e = ++n) r = 0, r = 1 & e ? y[e] ^ 255 & t : y[e] ^ t >> 8 & 255, this.perm[e] = this.perm[e + 256] = r, P.push(this.gradP[e] = this.gradP[e + 256] = v[r % 12]);
		    return P
		}, t.prototype.noise = function (t, e, n)
		{
		    var P, r, i, s, a, u, h, c, p, l, f, d, m, S, y, g, w, j, v, x, _, b, T, M, C, A, q, E, z, R, F;
		    return S = (t + e + n) * (1 / 3), a = Math.floor(t + S), c = Math.floor(e + S), E = n - (f = Math.floor(n + S)) + (y = (a + c + f) * o), (x = t - a + y) >= (M = e - c + y) ? M >= E ? (u = 1, p = 0, d = 0, h = 1, l = 1, m = 0) : x >= E ? (u = 1, p = 0, d = 0, h = 1,
			    l = 0, m = 1) : (u = 0, p = 0, d = 1, h = 1, l = 0, m = 1) : M < E ? (u = 0, p = 0, d = 1, h = 0, l = 1, m = 1) : x < E ? (u = 0, p = 1, d = 0, h = 0, l = 1, m = 1) : (u = 0, p = 1, d = 0, h = 1, l = 1, m = 0), _ = x - u + o, C = M - p + o, z = E - d + o, b = x - h + 2 * o, A = M - l + 2 * o, R = E - m + 2 * o, T = x - 1 + .5, q = M - 1 + .5, F = E - 1 + .5, a &= 255, c &= 255, f &= 255,
			P = this.gradP[a + this.perm[c + this.perm[f]]], r = this.gradP[a + u + this.perm[c + p + this.perm[f + d]]], i = this.gradP[a + h + this.perm[c + l + this.perm[f + m]]], s = this.gradP[a + 1 + this.perm[c + 1 + this.perm[f + 1]]],
			32 * (((g = .5 - x * x - M * M - E * E) < 0 ? 0 : (g *= g) * g * P.dot(x, M, E)) + ((w = .5 - _ * _ - C * C - z * z) < 0 ? 0 : (w *= w) * w * r.dot(_, C, z)) + ((j = .5 - b * b - A * A - R * R) < 0 ? 0 : (j *= j) * j * i.dot(b, A, R)) + ((v = .5 - T * T - q * q - F * F) < 0 ? 0 : (v *= v) * v * s.dot(T, q, F)))
		}, t
	    }(), M.BvhParser = function ()
	    {
		"use strict";

		function t(e, n, P, r)
		{
		    this.message = e, this.expected = n, this.found = P, this.location = r, this.name = "SyntaxError", "function" == typeof Error.captureStackTrace && Error.captureStackTrace(this, t)
		}
		return function (t, e)
		{
		    function n()
		    {
			this.constructor = t
		    }
		    n.prototype = e.prototype, t.prototype = new n
		}(t, Error),
		{
		    SyntaxError: t,
		    parse: function (e)
		    {
			var n, P = arguments.length > 1 ? arguments[1] :
			    {},
			    r = {},
			    i = {
				program: xt
			    },
			    s = xt,
			    a = "hierarchy",
			    o = {
				type: "literal",
				value: "HIERARCHY",
				description: '"HIERARCHY"'
			    },
			    u = function (t, e)
			    {
				return {
				    root: t,
				    motion: e
				}
			    },
			    h = "root",
			    c = {
				type: "literal",
				value: "ROOT",
				description: '"ROOT"'
			    },
			    p = "{",
			    l = {
				type: "literal",
				value: "{",
				description: '"{"'
			    },
			    f = "}",
			    d = {
				type: "literal",
				value: "}",
				description: '"}"'
			    },
			    m = function (t, e, n, P)
			    {
				return {
				    id: t,
				    offset: e,
				    channels: n,
				    joints: P
				}
			    },
			    S = "joint",
			    y = {
				type: "literal",
				value: "JOINT",
				description: '"JOINT"'
			    },
			    g = function (t, e, n, P)
			    {
				return {
				    type: "JOINT",
				    id: t,
				    offset: e,
				    channels: n,
				    joints: P
				}
			    },
			    w = "end site",
			    j = {
				type: "literal",
				value: "END SITE",
				description: '"END SITE"'
			    },
			    v = function (t)
			    {
				return {
				    type: "END SITE",
				    offset: t
				}
			    },
			    x = "offset",
			    _ = {
				type: "literal",
				value: "OFFSET",
				description: '"OFFSET"'
			    },
			    b = function (t, e, n)
			    {
				return {
				    x: t,
				    y: e,
				    z: n
				}
			    },
			    T = "channels",
			    M = {
				type: "literal",
				value: "CHANNELS",
				description: '"CHANNELS"'
			    },
			    C = /^[0-9]/,
			    A = {
				type: "class",
				value: "[0-9]",
				description: "[0-9]"
			    },
			    q = function (t, e)
			    {
				return e
			    },
			    E = "xposition",
			    z = {
				type: "literal",
				value: "Xposition",
				description: '"Xposition"'
			    },
			    R = "yposition",
			    F = {
				type: "literal",
				value: "Yposition",
				description: '"Yposition"'
			    },
			    I = "zposition",
			    D = {
				type: "literal",
				value: "Zposition",
				description: '"Zposition"'
			    },
			    L = "xrotation",
			    U = {
				type: "literal",
				value: "Xrotation",
				description: '"Xrotation"'
			    },
			    k = "yrotation",
			    O = {
				type: "literal",
				value: "Yrotation",
				description: '"Yrotation"'
			    },
			    Z = "zrotation",
			    X = {
				type: "literal",
				value: "Zrotation",
				description: '"Zrotation"'
			    },
			    B = function (t)
			    {
				return t
			    },
			    N = "motion",
			    Y = {
				type: "literal",
				value: "MOTION",
				description: '"MOTION"'
			    },
			    W = "frames:",
			    G = {
				type: "literal",
				value: "Frames:",
				description: '"Frames:"'
			    },
			    H = "frame time:",
			    Q = {
				type: "literal",
				value: "Frame Time:",
				description: '"Frame Time:"'
			    },
			    J = function (t, e, n)
			    {
				return {
				    frameCount: t,
				    frameTime: e,
				    frames: n
				}
			    },
			    V = /^[\n\r]/,
			    K = {
				type: "class",
				value: "[\\n\\r]",
				description: "[\\n\\r]"
			    },
			    $ = function (t)
			    {
				return t
			    },
			    tt = /^[ ]/,
			    et = {
				type: "class",
				value: "[ ]",
				description: "[ ]"
			    },
			    nt = function (t)
			    {
				return t
			    },
			    Pt = /^[a-zA-Z0-9\-_]/,
			    rt = {
				type: "class",
				value: "[a-zA-Z0-9-_]",
				description: "[a-zA-Z0-9-_]"
			    },
			    it = /^[\-0-9.e]/,
			    st = {
				type: "class",
				value: "[-0-9.e]",
				description: "[-0-9.e]"
			    },
			    at = function (t)
			    {
				return parseFloat(t.join(""))
			    },
			    ot = /^[\-0-9e]/,
			    ut = {
				type: "class",
				value: "[-0-9e]",
				description: "[-0-9e]"
			    },
			    ht = function (t)
			    {
				return parseInt(t.join(""))
			    },
			    ct = /^[ \t\n\r]/,
			    pt = {
				type: "class",
				value: "[ \\t\\n\\r]",
				description: "[ \\t\\n\\r]"
			    },
			    lt = function () {},
			    ft = 0,
			    dt = [
			    {
				line: 1,
				column: 1,
				seenCR: !1
			    }],
			    mt = 0,
			    St = [],
			    yt = 0;
			if ("startRule" in P)
			{
			    if (!(P.startRule in i)) throw new Error("Can't start parsing from rule \"" + P.startRule + '".');
			    s = i[P.startRule]
			}

			function gt(t)
			{
			    var n, P, r = dt[t];
			    if (r) return r;
			    for (n = t - 1; !dt[n];) n--;
			    for (r = {
				    line: (r = dt[n]).line,
				    column: r.column,
				    seenCR: r.seenCR
				}; n < t;) "\n" === (P = e.charAt(n)) ? (r.seenCR || r.line++, r.column = 1, r.seenCR = !1) : "\r" === P || "\u2028" === P || "\u2029" === P ? (r.line++, r.column = 1, r.seenCR = !0) : (r.column++, r.seenCR = !1), n++;
			    return dt[t] = r, r
			}

			function wt(t, e)
			{
			    var n = gt(t),
				P = gt(e);
			    return {
				start:
				{
				    offset: t,
				    line: n.line,
				    column: n.column
				},
				end:
				{
				    offset: e,
				    line: P.line,
				    column: P.column
				}
			    }
			}

			function jt(t)
			{
			    ft < mt || (ft > mt && (mt = ft, St = []), St.push(t))
			}

			function vt(e, n, P, r)
			{
			    return null !== n && function (t)
			    {
				var e = 1;
				for (t.sort(function (t, e)
				    {
					return t.description < e.description ? -1 : t.description > e.description ? 1 : 0
				    }); e < t.length;) t[e - 1] === t[e] ? t.splice(e, 1) : e++
			    }(n), new t(null !== e ? e : function (t, e)
			    {
				var n, P = new Array(t.length);
				for (n = 0; n < t.length; n++) P[n] = t[n].description;
				return "Expected " + (t.length > 1 ? P.slice(0, -1).join(", ") + " or " + P[t.length - 1] : P[0]) + " but " + (e ? '"' + function (t)
				{
				    function e(t)
				    {
					return t.charCodeAt(0).toString(16).toUpperCase()
				    }
				    return t.replace(/\\/g, "\\\\").replace(/"/g, '\\"').replace(/\x08/g, "\\b").replace(/\t/g, "\\t").replace(/\n/g, "\\n").replace(/\f/g, "\\f").replace(/\r/g, "\\r").replace(/[\x00-\x07\x0B\x0E\x0F]/g, function (t)
				    {
					return "\\x0" + e(t)
				    }).replace(/[\x10-\x1F\x80-\xFF]/g, function (t)
				    {
					return "\\x" + e(t)
				    }).replace(/[\u0100-\u0FFF]/g, function (t)
				    {
					return "\\u0" + e(t)
				    }).replace(/[\u1000-\uFFFF]/g, function (t)
				    {
					return "\\u" + e(t)
				    })
				}(e) + '"' : "end of input") + " found."
			    }(n, P), n, P, r)
			}

			function xt()
			{
			    var t, n, P, i;
			    return t = ft, e.substr(ft, 9).toLowerCase() === a ? (n = e.substr(ft, 9), ft += 9) : (n = r, 0 === yt && jt(o)), n !== r && zt() !== r && (P = function ()
			    {
				var t, n, P, i, s, a, o, u;
				if (t = ft, e.substr(ft, 4).toLowerCase() === h ? (n = e.substr(ft, 4), ft += 4) : (n = r,
					0 === yt && jt(c)), n !== r)
				    if (zt() !== r)
					if ((P = qt()) !== r)
					    if (zt() !== r)
						if (123 === e.charCodeAt(ft) ? (i = p, ft++) : (i = r, 0 === yt && jt(l)), i !== r)
						    if (zt() !== r)
							if ((s = bt()) !== r)
							    if (zt() !== r)
								if ((a = Tt()) !== r)
								    if (zt() !== r)
								    {
									for (o = [], u = _t(); u !== r;) o.push(u), u = _t();
									o !== r ? (125 === e.charCodeAt(ft) ? (u = f, ft++) : (u = r, 0 === yt && jt(d)), u !== r ? (n = m(P, s, a, o), t = n) : (ft = t, t = r)) : (ft = t, t = r)
								    }
				else ft = t, t = r;
				else ft = t, t = r;
				else ft = t, t = r;
				else ft = t, t = r;
				else ft = t, t = r;
				else ft = t, t = r;
				else ft = t, t = r;
				else ft = t, t = r;
				else ft = t,
				    t = r;
				else ft = t, t = r;
				return t
			    }()) !== r && zt() !== r && (i = function ()
			    {
				var t, n, P, i, s, a, o, u;
				if (t = ft, e.substr(ft, 6).toLowerCase() === N ? (n = e.substr(ft, 6), ft += 6) : (n = r, 0 === yt && jt(Y)), n !== r)
				    if (zt() !== r)
					if (e.substr(ft, 7).toLowerCase() === W ? (P = e.substr(ft, 7),
						ft += 7) : (P = r, 0 === yt && jt(G)), P !== r)
					    if (zt() !== r)
						if ((i = function ()
						    {
							var t, n;
							if (ft, t = [], ot.test(e.charAt(ft)) ? (n = e.charAt(ft), ft++) : (n = r, 0 === yt && jt(ut)), n !== r)
							    for (; n !== r;) t.push(n), ot.test(e.charAt(ft)) ? (n = e.charAt(ft), ft++) : (n = r,
								0 === yt && jt(ut));
							else t = r;
							return t !== r && (t = ht(t)), t
						    }()) !== r)
						    if (zt() !== r)
							if (e.substr(ft, 11).toLowerCase() === H ? (s = e.substr(ft, 11), ft += 11) : (s = r, 0 === yt && jt(Q)), s !== r)
							    if (zt() !== r)
								if ((a = Et()) !== r)
								    if (zt() !== r)
								    {
									for (o = [], u = Ct(); u !== r;) o.push(u),
									    u = Ct();
									o !== r ? (n = J(i, a, o), t = n) : (ft = t, t = r)
								    }
				else ft = t, t = r;
				else ft = t, t = r;
				else ft = t, t = r;
				else ft = t, t = r;
				else ft = t, t = r;
				else ft = t, t = r;
				else ft = t, t = r;
				else ft = t, t = r;
				else ft = t, t = r;
				else ft = t, t = r;
				return t
			    }()) !== r ? t = n = u(P, i) : (ft = t, t = r), t
			}

			function _t()
			{
			    var t, n, P, i, s, a, o, u;
			    if (t = ft, e.substr(ft, 5).toLowerCase() === S ? (n = e.substr(ft, 5), ft += 5) : (n = r, 0 === yt && jt(y)), n !== r)
				if (zt() !== r)
				    if ((P = qt()) !== r)
					if (zt() !== r)
					    if (123 === e.charCodeAt(ft) ? (i = p, ft++) : (i = r, 0 === yt && jt(l)),
						i !== r)
						if (zt() !== r)
						    if ((s = bt()) !== r)
							if (zt() !== r)
							    if ((a = Tt()) !== r)
								if (zt() !== r)
								{
								    for (o = [], u = _t(); u !== r;) o.push(u), u = _t();
								    o !== r ? (125 === e.charCodeAt(ft) ? (u = f, ft++) : (u = r, 0 === yt && jt(d)), u !== r && zt() !== r ? t = n = g(P, s, a, o) : (ft = t, t = r)) : (ft = t, t = r)
								}
			    else ft = t,
				t = r;
			    else ft = t, t = r;
			    else ft = t, t = r;
			    else ft = t, t = r;
			    else ft = t, t = r;
			    else ft = t, t = r;
			    else ft = t, t = r;
			    else ft = t, t = r;
			    else ft = t, t = r;
			    else ft = t, t = r;
			    return t === r && (t = ft, e.substr(ft, 8).toLowerCase() === w ? (n = e.substr(ft, 8), ft += 8) : (n = r, 0 === yt && jt(j)),
				n !== r && zt() !== r ? (123 === e.charCodeAt(ft) ? (P = p, ft++) : (P = r, 0 === yt && jt(l)), P !== r && zt() !== r && (i = bt()) !== r && zt() !== r ? (125 === e.charCodeAt(ft) ? (s = f, ft++) : (s = r, 0 === yt && jt(d)), s !== r && zt() !== r ? t = n = v(i) : (ft = t, t = r)) : (ft = t, t = r)) : (ft = t, t = r)), t
			}

			function bt()
			{
			    var t, n, P, i, s;
			    return t = ft, e.substr(ft, 6).toLowerCase() === x ? (n = e.substr(ft, 6), ft += 6) : (n = r, 0 === yt && jt(_)), n !== r && zt() !== r && (P = Et()) !== r && zt() !== r && (i = Et()) !== r && zt() !== r && (s = Et()) !== r ? t = n = b(P, i, s) : (ft = t, t = r), t
			}

			function Tt()
			{
			    var t, n, P, i, s;
			    if (t = ft, e.substr(ft, 8).toLowerCase() === T ? (n = e.substr(ft, 8), ft += 8) : (n = r, 0 === yt && jt(M)), n !== r)
				if (zt() !== r)
				    if (C.test(e.charAt(ft)) ? (P = e.charAt(ft), ft++) : (P = r, 0 === yt && jt(A)), P !== r)
					if (zt() !== r)
					{
					    for (i = [], s = Mt(); s !== r;) i.push(s),
						s = Mt();
					    i !== r ? t = n = q(P, i) : (ft = t, t = r)
					}
			    else ft = t, t = r;
			    else ft = t, t = r;
			    else ft = t, t = r;
			    else ft = t, t = r;
			    return t
			}

			function Mt()
			{
			    var t, n;
			    return t = ft, e.substr(ft, 9).toLowerCase() === E ? (n = e.substr(ft, 9), ft += 9) : (n = r, 0 === yt && jt(z)),
				n === r && (e.substr(ft, 9).toLowerCase() === R ? (n = e.substr(ft, 9), ft += 9) : (n = r, 0 === yt && jt(F)), n === r && (e.substr(ft, 9).toLowerCase() === I ? (n = e.substr(ft, 9), ft += 9) : (n = r, 0 === yt && jt(D)), n === r && (e.substr(ft, 9).toLowerCase() === L ? (n = e.substr(ft, 9),
				    ft += 9) : (n = r, 0 === yt && jt(U)), n === r && (e.substr(ft, 9).toLowerCase() === k ? (n = e.substr(ft, 9), ft += 9) : (n = r, 0 === yt && jt(O)), n === r && (e.substr(ft, 9).toLowerCase() === Z ? (n = e.substr(ft, 9), ft += 9) : (n = r, 0 === yt && jt(X))))))), n !== r && zt() !== r ? t = n = B(n) : (ft = t, t = r),
				t
			}

			function Ct()
			{
			    var t, n, P, i;
			    if (t = ft, n = [], (P = At()) !== r)
				for (; P !== r;) n.push(P), P = At();
			    else n = r;
			    if (n !== r)
			    {
				if (P = [], V.test(e.charAt(ft)) ? (i = e.charAt(ft), ft++) : (i = r, 0 === yt && jt(K)), i !== r)
				    for (; i !== r;) P.push(i), V.test(e.charAt(ft)) ? (i = e.charAt(ft),
					ft++) : (i = r, 0 === yt && jt(K));
				else P = r;
				P !== r ? t = n = $(n) : (ft = t, t = r)
			    }
			    else ft = t, t = r;
			    return t
			}

			function At()
			{
			    var t, n, P, i;
			    if (t = ft, (n = Et()) !== r)
			    {
				for (P = [], tt.test(e.charAt(ft)) ? (i = e.charAt(ft), ft++) : (i = r, 0 === yt && jt(et)); i !== r;) P.push(i),
				    tt.test(e.charAt(ft)) ? (i = e.charAt(ft), ft++) : (i = r, 0 === yt && jt(et));
				P !== r ? t = n = nt(n) : (ft = t, t = r)
			    }
			    else ft = t, t = r;
			    return t
			}

			function qt()
			{
			    var t, n, P;
			    if (t = ft, n = [], Pt.test(e.charAt(ft)) ? (P = e.charAt(ft), ft++) : (P = r, 0 === yt && jt(rt)),
				P !== r)
				for (; P !== r;) n.push(P), Pt.test(e.charAt(ft)) ? (P = e.charAt(ft), ft++) : (P = r, 0 === yt && jt(rt));
			    else n = r;
			    return t = n !== r ? e.substring(t, ft) : n
			}

			function Et()
			{
			    var t, n;
			    if (ft, t = [], it.test(e.charAt(ft)) ? (n = e.charAt(ft), ft++) : (n = r, 0 === yt && jt(st)),
				n !== r)
				for (; n !== r;) t.push(n), it.test(e.charAt(ft)) ? (n = e.charAt(ft), ft++) : (n = r, 0 === yt && jt(st));
			    else t = r;
			    return t !== r && (t = at(t)), t
			}

			function zt()
			{
			    var t, n;
			    for (ft, t = [], ct.test(e.charAt(ft)) ? (n = e.charAt(ft), ft++) : (n = r,
				    0 === yt && jt(pt)); n !== r;) t.push(n), ct.test(e.charAt(ft)) ? (n = e.charAt(ft), ft++) : (n = r, 0 === yt && jt(pt));
			    return t !== r && (t = lt()), t
			}
			if ((n = s()) !== r && ft === e.length) return n;
			throw n !== r && ft < e.length && jt(
			    {
				type: "end",
				description: "end of input"
			    }),
			    vt(null, St, mt < e.length ? e.charAt(mt) : null, mt < e.length ? wt(mt, mt + 1) : wt(mt, mt))
		    }
		}
	    }()
    }(console.error());; 
    
