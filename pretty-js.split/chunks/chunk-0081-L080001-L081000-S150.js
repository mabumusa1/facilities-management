        path: "directory/:id",
        element: e.jsx(PJ, {}),
        nav: !0
    }],
    HJ = Dt.lazy(() => SZ(() => rr(() => import("./PrivacyPolicy-Cj9ZJ-mU.js"), __vite__mapDeps([33, 1, 2, 3, 6])))),
    NJ = Dt.lazy(() => SZ(() => rr(() => import("./TermsAndConditions-Cg-fxBV4.js"), __vite__mapDeps([34, 1, 2, 3, 6])))),
    RJ = Dt.lazy(() => SZ(() => rr(() => import("./EditUserForm-DKh23qwh.js"), __vite__mapDeps([35, 1, 2, 3, 29, 6])))),
    YJ = [{
        title: "Edit-profile",
        path: "edit-profile",
        element: e.jsx(RJ, {}),
        nav: !0
    }, {
        title: "Privacy-policy",
        path: "privacy_policy",
        element: e.jsx(HJ, {}),
        nav: !0
    }, {
        title: "terms-and-conditions",
        path: "terms_and_conditions",
        element: e.jsx(NJ, {}),
        nav: !0
    }],
    BJ = Dt.lazy(() => SZ(() => rr(() => Promise.resolve().then(() => a4), void 0))),
    zJ = Dt.lazy(() => SZ(() => rr(() => import("./create-request.page-BXI9auEC.js"), __vite__mapDeps([36, 1, 2, 3, 37, 38, 12, 8, 9, 10, 6, 11])))),
    UJ = Dt.lazy(() => SZ(() => rr(() => import("./request-details.page-5hw1WRdm.js"), __vite__mapDeps([39, 1, 2, 3, 40, 41, 42, 6])))),
    WJ = [{
        title: "Requests",
        path: "requests",
        children: [{
            title: "Requests",
            path: "",
            element: e.jsx(BJ, {
                isHistory: 0
            })
        }, {
            title: "history",
            path: "history",
            element: e.jsx(BJ, {
                isHistory: 1
            })
        }, {
            title: "create-request",
            path: "create",
            element: e.jsx(zJ, {})
        }, {
            title: "view-request",
            path: ":id",
            element: e.jsx(UJ, {})
        }]
    }],
    ZJ = async () => (await lo("/api-management/marketplace/admin/settings/banks")).data, qJ = async e => await co("/api-management/marketplace/admin/settings/banks/store", e), $J = async () => await lo("/api-management/marketplace/admin/settings/visits"), GJ = async e => await co("/api-management/marketplace/admin/settings/sales/store", e), KJ = async () => lo("/api-management/marketplace/admin/settings/sales");
var QJ, JJ;
var XJ, eX, tX = function() {
    if (JJ) return QJ;

    function e(e) {
        this._maxSize = e, this.clear()
    }
    JJ = 1, e.prototype.clear = function() {
        this._size = 0, this._values = Object.create(null)
    }, e.prototype.get = function(e) {
        return this._values[e]
    }, e.prototype.set = function(e, t) {
        return this._size >= this._maxSize && this.clear(), e in this._values || this._size++, this._values[e] = t
    };
    var t = /[^.^\]^[]+|(?=\[\]|\.\.)/g,
        n = /^\d+$/,
        r = /^\d/,
        a = /[~`!#$%\^&*+=\-\[\]\\';,/{}|\\":<>\?]/g,
        i = /^\s*(['"]?)(.*?)(\1)\s*$/,
        o = new e(512),
        s = new e(512),
        l = new e(512);

    function d(e) {
        return o.get(e) || o.set(e, c(e).map(function(e) {
            return e.replace(i, "$2")
        }))
    }

    function c(e) {
        return e.match(t) || [""]
    }

    function u(e) {
        return "string" == typeof e && e && -1 !== ["'", '"'].indexOf(e.charAt(0))
    }

    function p(e) {
        return !u(e) && (function(e) {
            return e.match(r) && !e.match(n)
        }(e) || function(e) {
            return a.test(e)
        }(e))
    }
    return QJ = {
        Cache: e,
        split: c,
        normalizePath: d,
        setter: function(e) {
            var t = d(e);
            return s.get(e) || s.set(e, function(e, n) {
                for (var r = 0, a = t.length, i = e; r < a - 1;) {
                    var o = t[r];
                    if ("__proto__" === o || "constructor" === o || "prototype" === o) return e;
                    i = i[t[r++]]
                }
                i[t[r]] = n
            })
        },
        getter: function(e, t) {
            var n = d(e);
            return l.get(e) || l.set(e, function(e) {
                for (var r = 0, a = n.length; r < a;) {
                    if (null == e && t) return;
                    e = e[n[r++]]
                }
                return e
            })
        },
        join: function(e) {
            return e.reduce(function(e, t) {
                return e + (u(t) || n.test(t) ? "[" + t + "]" : (e ? "." : "") + t)
            }, "")
        },
        forEach: function(e, t, n) {
            ! function(e, t, n) {
                var r, a, i, o, s = e.length;
                for (a = 0; a < s; a++)(r = e[a]) && (p(r) && (r = '"' + r + '"'), i = !(o = u(r)) && /^\d+$/.test(r), t.call(n, r, o, i, a, e))
            }(Array.isArray(e) ? e : c(e), t, n)
        }
    }, QJ
}();
var nX, rX = function() {
        if (eX) return XJ;
        eX = 1;
        const e = /[A-Z\xc0-\xd6\xd8-\xde]?[a-z\xdf-\xf6\xf8-\xff]+(?:['’](?:d|ll|m|re|s|t|ve))?(?=[\xac\xb1\xd7\xf7\x00-\x2f\x3a-\x40\x5b-\x60\x7b-\xbf\u2000-\u206f \t\x0b\f\xa0\ufeff\n\r\u2028\u2029\u1680\u180e\u2000\u2001\u2002\u2003\u2004\u2005\u2006\u2007\u2008\u2009\u200a\u202f\u205f\u3000]|[A-Z\xc0-\xd6\xd8-\xde]|$)|(?:[A-Z\xc0-\xd6\xd8-\xde]|[^\ud800-\udfff\xac\xb1\xd7\xf7\x00-\x2f\x3a-\x40\x5b-\x60\x7b-\xbf\u2000-\u206f \t\x0b\f\xa0\ufeff\n\r\u2028\u2029\u1680\u180e\u2000\u2001\u2002\u2003\u2004\u2005\u2006\u2007\u2008\u2009\u200a\u202f\u205f\u3000\d+\u2700-\u27bfa-z\xdf-\xf6\xf8-\xffA-Z\xc0-\xd6\xd8-\xde])+(?:['’](?:D|LL|M|RE|S|T|VE))?(?=[\xac\xb1\xd7\xf7\x00-\x2f\x3a-\x40\x5b-\x60\x7b-\xbf\u2000-\u206f \t\x0b\f\xa0\ufeff\n\r\u2028\u2029\u1680\u180e\u2000\u2001\u2002\u2003\u2004\u2005\u2006\u2007\u2008\u2009\u200a\u202f\u205f\u3000]|[A-Z\xc0-\xd6\xd8-\xde](?:[a-z\xdf-\xf6\xf8-\xff]|[^\ud800-\udfff\xac\xb1\xd7\xf7\x00-\x2f\x3a-\x40\x5b-\x60\x7b-\xbf\u2000-\u206f \t\x0b\f\xa0\ufeff\n\r\u2028\u2029\u1680\u180e\u2000\u2001\u2002\u2003\u2004\u2005\u2006\u2007\u2008\u2009\u200a\u202f\u205f\u3000\d+\u2700-\u27bfa-z\xdf-\xf6\xf8-\xffA-Z\xc0-\xd6\xd8-\xde])|$)|[A-Z\xc0-\xd6\xd8-\xde]?(?:[a-z\xdf-\xf6\xf8-\xff]|[^\ud800-\udfff\xac\xb1\xd7\xf7\x00-\x2f\x3a-\x40\x5b-\x60\x7b-\xbf\u2000-\u206f \t\x0b\f\xa0\ufeff\n\r\u2028\u2029\u1680\u180e\u2000\u2001\u2002\u2003\u2004\u2005\u2006\u2007\u2008\u2009\u200a\u202f\u205f\u3000\d+\u2700-\u27bfa-z\xdf-\xf6\xf8-\xffA-Z\xc0-\xd6\xd8-\xde])+(?:['’](?:d|ll|m|re|s|t|ve))?|[A-Z\xc0-\xd6\xd8-\xde]+(?:['’](?:D|LL|M|RE|S|T|VE))?|\d*(?:1ST|2ND|3RD|(?![123])\dTH)(?=\b|[a-z_])|\d*(?:1st|2nd|3rd|(?![123])\dth)(?=\b|[A-Z_])|\d+|(?:[\u2700-\u27bf]|(?:\ud83c[\udde6-\uddff]){2}|[\ud800-\udbff][\udc00-\udfff])[\ufe0e\ufe0f]?(?:[\u0300-\u036f\ufe20-\ufe2f\u20d0-\u20ff]|\ud83c[\udffb-\udfff])?(?:\u200d(?:[^\ud800-\udfff]|(?:\ud83c[\udde6-\uddff]){2}|[\ud800-\udbff][\udc00-\udfff])[\ufe0e\ufe0f]?(?:[\u0300-\u036f\ufe20-\ufe2f\u20d0-\u20ff]|\ud83c[\udffb-\udfff])?)*/g,
            t = t => t.match(e) || [],
            n = e => e[0].toUpperCase() + e.slice(1),
            r = (e, n) => t(e).join(n).toLowerCase(),
            a = e => t(e).reduce((e, t) => `${e}${e?t[0].toUpperCase()+t.slice(1).toLowerCase():t.toLowerCase()}`, "");
        return XJ = {
            words: t,
            upperFirst: n,
            camelCase: a,
            pascalCase: e => n(a(e)),
            snakeCase: e => r(e, "_"),
            kebabCase: e => r(e, "-"),
            sentenceCase: e => n(r(e, " ")),
            titleCase: e => t(e).map(n).join(" ")
        }
    }(),
    aX = {
        exports: {}
    };
var iX = function() {
    if (nX) return aX.exports;

    function e(e, t) {
        var n = e.length,
            r = new Array(n),
            a = {},
            i = n,
            o = function(e) {
                for (var t = new Map, n = 0, r = e.length; n < r; n++) {
                    var a = e[n];
                    t.has(a[0]) || t.set(a[0], new Set), t.has(a[1]) || t.set(a[1], new Set), t.get(a[0]).add(a[1])
                }
                return t
            }(t),
            s = function(e) {
                for (var t = new Map, n = 0, r = e.length; n < r; n++) t.set(e[n], n);
                return t
            }(e);
        for (t.forEach(function(e) {
                if (!s.has(e[0]) || !s.has(e[1])) throw new Error("Unknown node. There is an unknown node in the supplied edges.")
            }); i--;) a[i] || l(e[i], i, new Set);
        return r;

        function l(e, t, i) {
            if (i.has(e)) {
                var d;
                try {
                    d = ", node was:" + JSON.stringify(e)
                } catch (ti) {
                    d = ""
                }
                throw new Error("Cyclic dependency" + d)
            }
            if (!s.has(e)) throw new Error("Found unknown node. Make sure to provided all involved nodes. Unknown node: " + JSON.stringify(e));
            if (!a[t]) {
                a[t] = !0;
                var c = o.get(e) || new Set;
                if (t = (c = Array.from(c)).length) {
                    i.add(e);
                    do {
                        var u = c[--t];
                        l(u, s.get(u), i)
                    } while (t);
                    i.delete(e)
                }
                r[--n] = e
            }
        }
    }
    return nX = 1, aX.exports = function(t) {
        return e(function(e) {
            for (var t = new Set, n = 0, r = e.length; n < r; n++) {
                var a = e[n];
                t.add(a[0]), t.add(a[1])
            }
            return Array.from(t)
        }(t), t)
    }, aX.exports.array = e, aX.exports
}();
const oX = It(iX),
    sX = Object.prototype.toString,
    lX = Error.prototype.toString,
    dX = RegExp.prototype.toString,
    cX = "undefined" != typeof Symbol ? Symbol.prototype.toString : () => "",
    uX = /^Symbol\((.*)\)(.*)$/;

function pX(e, t = !1) {
    if (null == e || !0 === e || !1 === e) return "" + e;
    const n = typeof e;
    if ("number" === n) return function(e) {
        return e != +e ? "NaN" : 0 === e && 1 / e < 0 ? "-0" : "" + e
    }(e);
    if ("string" === n) return t ? `"${e}"` : e;
    if ("function" === n) return "[Function " + (e.name || "anonymous") + "]";
    if ("symbol" === n) return cX.call(e).replace(uX, "Symbol($1)");
    const r = sX.call(e).slice(8, -1);
    return "Date" === r ? isNaN(e.getTime()) ? "" + e : e.toISOString(e) : "Error" === r || e instanceof Error ? "[" + lX.call(e) + "]" : "RegExp" === r ? dX.call(e) : null
}

function hX(e, t) {
    let n = pX(e, t);
    return null !== n ? n : JSON.stringify(e, function(e, n) {
        let r = pX(this[e], t);
        return null !== r ? r : n
    }, 2)
}

function mX(e) {
    return null == e ? [] : [].concat(e)
}
let fX, gX, yX, vX = /\$\{\s*(\w+)\s*\}/g;
fX = Symbol.toStringTag;
class _X {
    constructor(e, t, n, r) {
        this.name = void 0, this.message = void 0, this.value = void 0, this.path = void 0, this.type = void 0, this.params = void 0, this.errors = void 0, this.inner = void 0, this[fX] = "Error", this.name = "ValidationError", this.value = t, this.path = n, this.type = r, this.errors = [], this.inner = [], mX(e).forEach(e => {
            if (xX.isError(e)) {
                this.errors.push(...e.errors);
                const t = e.inner.length ? e.inner : [e];
                this.inner.push(...t)
            } else this.errors.push(e)
        }), this.message = this.errors.length > 1 ? `${this.errors.length} errors occurred` : this.errors[0]
    }
}
gX = Symbol.hasInstance, yX = Symbol.toStringTag;
class xX extends Error {
    static formatError(e, t) {
        const n = t.label || t.path || "this";
        return t = Object.assign({}, t, {
            path: n,
            originalPath: t.path
        }), "string" == typeof e ? e.replace(vX, (e, n) => hX(t[n])) : "function" == typeof e ? e(t) : e
    }
    static isError(e) {
        return e && "ValidationError" === e.name
    }
    constructor(e, t, n, r, a) {
        const i = new _X(e, t, n, r);
        if (a) return i;
        super(), this.value = void 0, this.path = void 0, this.type = void 0, this.params = void 0, this.errors = [], this.inner = [], this[yX] = "Error", this.name = i.name, this.message = i.message, this.type = i.type, this.value = i.value, this.path = i.path, this.errors = i.errors, this.inner = i.inner, Error.captureStackTrace && Error.captureStackTrace(this, xX)
    }
    static[gX](e) {
        return _X[Symbol.hasInstance](e) || super[Symbol.hasInstance](e)
    }
}
let bX = {
        default: "${path} is invalid",
        required: "${path} is a required field",
        defined: "${path} must be defined",
        notNull: "${path} cannot be null",
        oneOf: "${path} must be one of the following values: ${values}",
        notOneOf: "${path} must not be one of the following values: ${values}",
        notType: ({
            path: e,
            type: t,
            value: n,
            originalValue: r
        }) => {
            const a = null != r && r !== n ? ` (cast from the value \`${hX(r,!0)}\`).` : ".";
            return "mixed" !== t ? `${e} must be a \`${t}\` type, but the final value was: \`${hX(n,!0)}\`` + a : `${e} must match the configured type. The validated value was: \`${hX(n,!0)}\`` + a
        }
    },
    wX = {
        length: "${path} must be exactly ${length} characters",
        min: "${path} must be at least ${min} characters",
        max: "${path} must be at most ${max} characters",
        matches: '${path} must match the following: "${regex}"',
        email: "${path} must be a valid email",
        url: "${path} must be a valid URL",
        uuid: "${path} must be a valid UUID",
        datetime: "${path} must be a valid ISO date-time",
        datetime_precision: "${path} must be a valid ISO date-time with a sub-second precision of exactly ${precision} digits",
        datetime_offset: '${path} must be a valid ISO date-time with UTC "Z" timezone',
        trim: "${path} must be a trimmed string",
        lowercase: "${path} must be a lowercase string",
        uppercase: "${path} must be a upper case string"
    },
    CX = {
        min: "${path} must be greater than or equal to ${min}",
        max: "${path} must be less than or equal to ${max}",
        lessThan: "${path} must be less than ${less}",
        moreThan: "${path} must be greater than ${more}",
        positive: "${path} must be a positive number",
        negative: "${path} must be a negative number",
        integer: "${path} must be an integer"
    },
    MX = {
        min: "${path} field must be later than ${min}",
        max: "${path} field must be at earlier than ${max}"
    },
    SX = {
        isValue: "${path} field must be ${value}"
    },
    LX = {
        noUnknown: "${path} field has unspecified keys: ${unknown}",
        exact: "${path} object contains unknown properties: ${properties}"
    },
    kX = {
        min: "${path} field must have at least ${min} items",
        max: "${path} field must have less than or equal to ${max} items",
        length: "${path} must have ${length} items"
    },
    TX = {
        notType: e => {
            const {
                path: t,
                value: n,
                spec: r
            } = e, a = r.types.length;
            if (Array.isArray(n)) {
                if (n.length < a) return `${t} tuple value has too few items, expected a length of ${a} but got ${n.length} for value: \`${hX(n,!0)}\``;
                if (n.length > a) return `${t} tuple value has too many items, expected a length of ${a} but got ${n.length} for value: \`${hX(n,!0)}\``
            }
            return xX.formatError(bX.notType, e)
        }
    };
Object.assign(Object.create(null), {
    mixed: bX,
    string: wX,
    number: CX,
    date: MX,
    object: LX,
    array: kX,
    boolean: SX,
    tuple: TX
});
const jX = e => e && e.__isYupSchema__;
class EX {
    static fromOptions(e, t) {
        if (!t.then && !t.otherwise) throw new TypeError("either `then:` or `otherwise:` is required for `when()` conditions");
        let {
            is: n,
            then: r,
            otherwise: a
        } = t, i = "function" == typeof n ? n : (...e) => e.every(e => e === n);
        return new EX(e, (e, t) => {
            var n;
            let o = i(...e) ? r : a;
            return null != (n = null == o ? void 0 : o(t)) ? n : t
        })
    }
    constructor(e, t) {
        this.fn = void 0, this.refs = e, this.refs = e, this.fn = t
    }
    resolve(e, t) {
        let n = this.refs.map(e => e.getValue(null == t ? void 0 : t.value, null == t ? void 0 : t.parent, null == t ? void 0 : t.context)),
            r = this.fn(n, e, t);
        if (void 0 === r || r === e) return e;
        if (!jX(r)) throw new TypeError("conditions must return a schema object");
        return r.resolve(t)
    }
}
const DX = "$",
    VX = ".";
class AX {
    constructor(e, t = {}) {
        if (this.key = void 0, this.isContext = void 0, this.isValue = void 0, this.isSibling = void 0, this.path = void 0, this.getter = void 0, this.map = void 0, "string" != typeof e) throw new TypeError("ref must be a string, got: " + e);
        if (this.key = e.trim(), "" === e) throw new TypeError("ref must be a non-empty string");
        this.isContext = this.key[0] === DX, this.isValue = this.key[0] === VX, this.isSibling = !this.isContext && !this.isValue;
        let n = this.isContext ? DX : this.isValue ? VX : "";
        this.path = this.key.slice(n.length), this.getter = this.path && tX.getter(this.path, !0), this.map = t.map
    }
    getValue(e, t, n) {
        let r = this.isContext ? n : this.isValue ? e : t;
        return this.getter && (r = this.getter(r || {})), this.map && (r = this.map(r)), r
    }
    cast(e, t) {
        return this.getValue(e, null == t ? void 0 : t.parent, null == t ? void 0 : t.context)
    }
    resolve() {
        return this
    }
    describe() {
        return {
            type: "ref",
            key: this.key
        }
    }
    toString() {
        return `Ref(${this.key})`
    }
    static isRef(e) {
        return e && e.__isYupRef
    }
}
AX.prototype.__isYupRef = !0;
const OX = e => null == e;

function PX(e) {
    function t({
        value: t,
        path: n = "",
        options: r,
        originalValue: a,
        schema: i
    }, o, s) {
        const {
            name: l,
            test: d,
            params: c,
            message: u,
            skipAbsent: p
        } = e;
        let {
            parent: h,
            context: m,
            abortEarly: f = i.spec.abortEarly,
            disableStackTrace: g = i.spec.disableStackTrace
        } = r;
        const y = {
            value: t,
            parent: h,
            context: m
        };

        function v(e = {}) {
            const r = IX(Object.assign({
                    value: t,
                    originalValue: a,
                    label: i.spec.label,
                    path: e.path || n,
                    spec: i.spec,
                    disableStackTrace: e.disableStackTrace || g
                }, c, e.params), y),
                o = new xX(xX.formatError(e.message || u, r), t, r.path, e.type || l, r.disableStackTrace);
            return o.params = r, o
        }
        const _ = f ? o : s;
        let x = {
            path: n,
            parent: h,
            type: l,
            from: r.from,
            createError: v,
            resolve: e => FX(e, y),
            options: r,
            originalValue: a,
            schema: i
        };
        const b = e => {
                xX.isError(e) ? _(e) : e ? s(null) : _(v())
            },
            w = e => {
                xX.isError(e) ? _(e) : o(e)
            };
        if (p && OX(t)) return b(!0);
        let C;
        try {
            var M;
            if (C = d.call(x, t, x), "function" == typeof(null == (M = C) ? void 0 : M.then)) {
                if (r.sync) throw new Error(`Validation test of type: "${x.type}" returned a Promise during a synchronous validate. This test will finish after the validate call has returned`);
                return Promise.resolve(C).then(b, w)
            }
        } catch (S) {
            return void w(S)
        }
        b(C)
    }
    return t.OPTIONS = e, t
}

function IX(e, t) {
    if (!e) return e;
    for (const n of Object.keys(e)) e[n] = FX(e[n], t);
    return e
}

function FX(e, t) {
    return AX.isRef(e) ? e.getValue(t.value, t.parent, t.context) : e
}

function HX(e, t, n, r = n) {
    let a, i, o;
    return t ? (tX.forEach(t, (s, l, d) => {
        let c = l ? s.slice(1, s.length - 1) : s,
            u = "tuple" === (e = e.resolve({
                context: r,
                parent: a,
                value: n
            })).type,
            p = d ? parseInt(c, 10) : 0;
        if (e.innerType || u) {
            if (u && !d) throw new Error(`Yup.reach cannot implicitly index into a tuple type. the path part "${o}" must contain an index to the tuple element, e.g. "${o}[0]"`);
            if (n && p >= n.length) throw new Error(`Yup.reach cannot resolve an array item at index: ${s}, in the path: ${t}. because there is no value at that index. `);
            a = n, n = n && n[p], e = u ? e.spec.types[p] : e.innerType
        }
        if (!d) {
            if (!e.fields || !e.fields[c]) throw new Error(`The schema does not contain the path: ${t}. (failed at: ${o} which is a type: "${e.type}")`);
            a = n, n = n && n[c], e = e.fields[c]
        }
        i = c, o = l ? "[" + s + "]" : "." + s
    }), {
        schema: e,
        parent: a,
        parentPath: i
    }) : {
        parent: a,
        parentPath: t,
        schema: e
    }
}
class NX extends Set {
    describe() {
        const e = [];
        for (const t of this.values()) e.push(AX.isRef(t) ? t.describe() : t);
        return e
    }
    resolveAll(e) {
        let t = [];
        for (const n of this.values()) t.push(e(n));
        return t
    }
    clone() {
        return new NX(this.values())
    }
    merge(e, t) {
        const n = this.clone();
        return e.forEach(e => n.add(e)), t.forEach(e => n.delete(e)), n
    }
}

function RX(e, t = new Map) {
    if (jX(e) || !e || "object" != typeof e) return e;
    if (t.has(e)) return t.get(e);
    let n;
    if (e instanceof Date) n = new Date(e.getTime()), t.set(e, n);
    else if (e instanceof RegExp) n = new RegExp(e), t.set(e, n);
    else if (Array.isArray(e)) {
        n = new Array(e.length), t.set(e, n);
        for (let r = 0; r < e.length; r++) n[r] = RX(e[r], t)
    } else if (e instanceof Map) {
        n = new Map, t.set(e, n);
        for (const [r, a] of e.entries()) n.set(r, RX(a, t))
    } else if (e instanceof Set) {
        n = new Set, t.set(e, n);
        for (const r of e) n.add(RX(r, t))
    } else {
        if (!(e instanceof Object)) throw Error(`Unable to clone ${e}`);
        n = {}, t.set(e, n);
        for (const [r, a] of Object.entries(e)) n[r] = RX(a, t)
    }
    return n
}

function YX(e) {
    if (null == e || !e.length) return;
    const t = [];
    let n = "",
        r = !1,
        a = !1;
    for (let i = 0; i < e.length; i++) {
        const o = e[i];
        "[" !== o || a ? "]" !== o || a ? '"' !== o ? "." !== o || r || a ? n += o : n && (t.push(n), n = "") : a = !a : (n && (/^\d+$/.test(n) ? t.push(n) : t.push(n.replace(/^"|"$/g, "")), n = ""), r = !1) : (n && (t.push(...n.split(".").filter(Boolean)), n = ""), r = !0)
    }
    return n && t.push(...n.split(".").filter(Boolean)), t
}

function BX(e, t) {
    var n;
    if ((null == (n = e.inner) || !n.length) && e.errors.length) return function(e, t) {
        const n = t ? `${t}.${e.path}` : e.path;
        return e.errors.map(e => ({
            message: e,
            path: YX(n)
        }))
    }(e, t);
    const r = t ? `${t}.${e.path}` : e.path;
    return e.inner.flatMap(e => BX(e, r))
}
class zX {
    constructor(e) {
        this.type = void 0, this.deps = [], this.tests = void 0, this.transforms = void 0, this.conditions = [], this._mutate = void 0, this.internalTests = {}, this._whitelist = new NX, this._blacklist = new NX, this.exclusiveTests = Object.create(null), this._typeCheck = void 0, this.spec = void 0, this.tests = [], this.transforms = [], this.withMutation(() => {
            this.typeError(bX.notType)
        }), this.type = e.type, this._typeCheck = e.check, this.spec = Object.assign({
            strip: !1,
            strict: !1,
            abortEarly: !0,
            recursive: !0,
            disableStackTrace: !1,
            nullable: !1,
            optional: !0,
            coerce: !0
        }, null == e ? void 0 : e.spec), this.withMutation(e => {
            e.nonNullable()
        })
    }
    get _type() {
        return this.type
    }
    clone(e) {
        if (this._mutate) return e && Object.assign(this.spec, e), this;
        const t = Object.create(Object.getPrototypeOf(this));
        return t.type = this.type, t._typeCheck = this._typeCheck, t._whitelist = this._whitelist.clone(), t._blacklist = this._blacklist.clone(), t.internalTests = Object.assign({}, this.internalTests), t.exclusiveTests = Object.assign({}, this.exclusiveTests), t.deps = [...this.deps], t.conditions = [...this.conditions], t.tests = [...this.tests], t.transforms = [...this.transforms], t.spec = RX(Object.assign({}, this.spec, e)), t
    }
    label(e) {
        let t = this.clone();
        return t.spec.label = e, t
    }
    meta(...e) {
        if (0 === e.length) return this.spec.meta;
        let t = this.clone();
        return t.spec.meta = Object.assign(t.spec.meta || {}, e[0]), t
    }
    withMutation(e) {
        let t = this._mutate;
        this._mutate = !0;
        let n = e(this);
        return this._mutate = t, n
    }
    concat(e) {
        if (!e || e === this) return this;
        if (e.type !== this.type && "mixed" !== this.type) throw new TypeError(`You cannot \`concat()\` schema's of different types: ${this.type} and ${e.type}`);
        let t = this,
            n = e.clone();
        const r = Object.assign({}, t.spec, n.spec);
        return n.spec = r, n.internalTests = Object.assign({}, t.internalTests, n.internalTests), n._whitelist = t._whitelist.merge(e._whitelist, e._blacklist), n._blacklist = t._blacklist.merge(e._blacklist, e._whitelist), n.tests = t.tests, n.exclusiveTests = t.exclusiveTests, n.withMutation(t => {
            e.tests.forEach(e => {
                t.test(e.OPTIONS)
            })
        }), n.transforms = [...t.transforms, ...n.transforms], n
    }
    isType(e) {
        return null == e ? !(!this.spec.nullable || null !== e) || !(!this.spec.optional || void 0 !== e) : this._typeCheck(e)
    }
    resolve(e) {
        let t = this;
        if (t.conditions.length) {
            let n = t.conditions;
            t = t.clone(), t.conditions = [], t = n.reduce((t, n) => n.resolve(t, e), t), t = t.resolve(e)
        }
        return t
    }
    resolveOptions(e) {
        var t, n, r, a;
        return Object.assign({}, e, {
            from: e.from || [],
            strict: null != (t = e.strict) ? t : this.spec.strict,
            abortEarly: null != (n = e.abortEarly) ? n : this.spec.abortEarly,
            recursive: null != (r = e.recursive) ? r : this.spec.recursive,
            disableStackTrace: null != (a = e.disableStackTrace) ? a : this.spec.disableStackTrace
        })
    }
    cast(e, t = {}) {
        let n = this.resolve(Object.assign({}, t, {
                value: e
            })),
            r = "ignore-optionality" === t.assert,
            a = n._cast(e, t);
        if (!1 !== t.assert && !n.isType(a)) {
            if (r && OX(a)) return a;
            let i = hX(e),
                o = hX(a);
            throw new TypeError(`The value of ${t.path||"field"} could not be cast to a value that satisfies the schema type: "${n.type}". \n\nattempted value: ${i} \n` + (o !== i ? `result of cast: ${o}` : ""))
        }
        return a
    }
    _cast(e, t) {
        let n = void 0 === e ? e : this.transforms.reduce((n, r) => r.call(this, n, e, this, t), e);
        return void 0 === n && (n = this.getDefault(t)), n
    }
    _validate(e, t = {}, n, r) {
        let {
            path: a,
            originalValue: i = e,
            strict: o = this.spec.strict
        } = t, s = e;
        o || (s = this._cast(s, Object.assign({
            assert: !1
        }, t)));
        let l = [];
        for (let d of Object.values(this.internalTests)) d && l.push(d);
        this.runTests({
            path: a,
            value: s,
            originalValue: i,
            options: t,
            tests: l
        }, n, e => {
            if (e.length) return r(e, s);
            this.runTests({
                path: a,
                value: s,
                originalValue: i,
                options: t,
                tests: this.tests
            }, n, r)
        })
    }
    runTests(e, t, n) {
        let r = !1,
            {
                tests: a,
                value: i,
                originalValue: o,
                path: s,
                options: l
            } = e,
            d = e => {
                r || (r = !0, t(e, i))
            },
            c = e => {
                r || (r = !0, n(e, i))
            },
            u = a.length,
            p = [];
        if (!u) return c([]);
        let h = {
            value: i,
            originalValue: o,
            path: s,
            options: l,
            schema: this
        };
        for (let m = 0; m < a.length; m++) {
            (0, a[m])(h, d, function(e) {
                e && (Array.isArray(e) ? p.push(...e) : p.push(e)), --u <= 0 && c(p)
            })
        }
    }
    asNestedTest({
        key: e,
        index: t,
        parent: n,
        parentPath: r,
        originalParent: a,
        options: i
    }) {
        const o = null != e ? e : t;
        if (null == o) throw TypeError("Must include `key` or `index` for nested validations");
        const s = "number" == typeof o;
        let l = n[o];
        const d = Object.assign({}, i, {
            strict: !0,
            parent: n,
            value: l,
            originalValue: a[o],
            key: void 0,
            [s ? "index" : "key"]: o,
            path: s || o.includes(".") ? `${r||""}[${s?o:`"${o}"`}]` : (r ? `${r}.` : "") + e
        });
        return (e, t, n) => this.resolve(d)._validate(l, d, t, n)
    }
    validate(e, t) {
        var n;
        let r = this.resolve(Object.assign({}, t, {
                value: e
            })),
            a = null != (n = null == t ? void 0 : t.disableStackTrace) ? n : r.spec.disableStackTrace;
        return new Promise((n, i) => r._validate(e, t, (e, t) => {
            xX.isError(e) && (e.value = t), i(e)
        }, (e, t) => {
            e.length ? i(new xX(e, t, void 0, void 0, a)) : n(t)
        }))
    }
    validateSync(e, t) {
        var n;
        let r, a = this.resolve(Object.assign({}, t, {
                value: e
            })),
            i = null != (n = null == t ? void 0 : t.disableStackTrace) ? n : a.spec.disableStackTrace;
        return a._validate(e, Object.assign({}, t, {
            sync: !0
        }), (e, t) => {
            throw xX.isError(e) && (e.value = t), e
        }, (t, n) => {
            if (t.length) throw new xX(t, e, void 0, void 0, i);
            r = n
        }), r
    }
    isValid(e, t) {
        return this.validate(e, t).then(() => !0, e => {
            if (xX.isError(e)) return !1;
            throw e
        })
    }
    isValidSync(e, t) {
        try {
            return this.validateSync(e, t), !0
        } catch (n) {
            if (xX.isError(n)) return !1;
            throw n
        }
    }
    _getDefault(e) {
        let t = this.spec.default;
        return null == t ? t : "function" == typeof t ? t.call(this, e) : RX(t)
    }
    getDefault(e) {
        return this.resolve(e || {})._getDefault(e)
    }
    default (e) {
        if (0 === arguments.length) return this._getDefault();
        return this.clone({
            default: e
        })
    }
    strict(e = !0) {
        return this.clone({
            strict: e
        })
    }
    nullability(e, t) {
        const n = this.clone({
            nullable: e
        });
        return n.internalTests.nullable = PX({
            message: t,
            name: "nullable",
            test(e) {
                return null !== e || this.schema.spec.nullable
            }
        }), n
    }
    optionality(e, t) {
        const n = this.clone({
            optional: e
        });
        return n.internalTests.optionality = PX({
            message: t,
            name: "optionality",
            test(e) {
                return void 0 !== e || this.schema.spec.optional
            }
        }), n
    }
    optional() {
        return this.optionality(!0)
    }
    defined(e = bX.defined) {
        return this.optionality(!1, e)
    }
    nullable() {
        return this.nullability(!0)
    }
    nonNullable(e = bX.notNull) {
        return this.nullability(!1, e)
    }
    required(e = bX.required) {
        return this.clone().withMutation(t => t.nonNullable(e).defined(e))
    }
    notRequired() {
        return this.clone().withMutation(e => e.nullable().optional())
    }
    transform(e) {
        let t = this.clone();
        return t.transforms.push(e), t
    }
    test(...e) {
        let t;
        if (t = 1 === e.length ? "function" == typeof e[0] ? {
                test: e[0]
            } : e[0] : 2 === e.length ? {
                name: e[0],
                test: e[1]
            } : {
                name: e[0],
                message: e[1],
                test: e[2]
            }, void 0 === t.message && (t.message = bX.default), "function" != typeof t.test) throw new TypeError("`test` is a required parameters");
        let n = this.clone(),
            r = PX(t),
            a = t.exclusive || t.name && !0 === n.exclusiveTests[t.name];
        if (t.exclusive && !t.name) throw new TypeError("Exclusive tests must provide a unique `name` identifying the test");
        return t.name && (n.exclusiveTests[t.name] = !!t.exclusive), n.tests = n.tests.filter(e => {
            if (e.OPTIONS.name === t.name) {
                if (a) return !1;
                if (e.OPTIONS.test === r.OPTIONS.test) return !1
            }
            return !0
        }), n.tests.push(r), n
    }
    when(e, t) {
        Array.isArray(e) || "string" == typeof e || (t = e, e = ".");
        let n = this.clone(),
            r = mX(e).map(e => new AX(e));
        return r.forEach(e => {
            e.isSibling && n.deps.push(e.key)
        }), n.conditions.push("function" == typeof t ? new EX(r, t) : EX.fromOptions(r, t)), n
    }
    typeError(e) {
        let t = this.clone();
        return t.internalTests.typeError = PX({
            message: e,
            name: "typeError",
            skipAbsent: !0,
            test(e) {
                return !!this.schema._typeCheck(e) || this.createError({
                    params: {
                        type: this.schema.type
                    }
                })
            }
        }), t
    }
    oneOf(e, t = bX.oneOf) {
        let n = this.clone();
        return e.forEach(e => {
            n._whitelist.add(e), n._blacklist.delete(e)
        }), n.internalTests.whiteList = PX({
            message: t,
            name: "oneOf",
            skipAbsent: !0,
            test(e) {
                let t = this.schema._whitelist,
                    n = t.resolveAll(this.resolve);
                return !!n.includes(e) || this.createError({
                    params: {
                        values: Array.from(t).join(", "),
                        resolved: n
                    }
                })
            }
        }), n
    }
    notOneOf(e, t = bX.notOneOf) {
        let n = this.clone();
        return e.forEach(e => {
            n._blacklist.add(e), n._whitelist.delete(e)
        }), n.internalTests.blacklist = PX({
            message: t,
            name: "notOneOf",
            test(e) {
                let t = this.schema._blacklist,
                    n = t.resolveAll(this.resolve);
                return !n.includes(e) || this.createError({
                    params: {
                        values: Array.from(t).join(", "),
                        resolved: n
                    }
                })
            }
        }), n
    }
    strip(e = !0) {
        let t = this.clone();
        return t.spec.strip = e, t
    }
    describe(e) {
        const t = (e ? this.resolve(e) : this).clone(),
            {
                label: n,
                meta: r,
                optional: a,
                nullable: i
            } = t.spec,
            o = {
                meta: r,
                label: n,
                optional: a,
                nullable: i,
                default: t.getDefault(e),
                type: t.type,
                oneOf: t._whitelist.describe(),
                notOneOf: t._blacklist.describe(),
                tests: t.tests.filter((e, t, n) => n.findIndex(t => t.OPTIONS.name === e.OPTIONS.name) === t).map(t => {
                    const n = t.OPTIONS.params && e ? IX(Object.assign({}, t.OPTIONS.params), e) : t.OPTIONS.params;
                    return {
                        name: t.OPTIONS.name,
                        params: n
                    }
                })
            };
        return o
    }