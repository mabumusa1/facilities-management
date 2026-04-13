            const e = document.head,
                t = document.createElement("link");
            t.rel = "icon", t.type = "image/svg+xml", t.href = `/assets/fav/${o}/favicon-32x32.png`, e.appendChild(t), document.title = a("portal") + " - " + a(o)
        });
        const d = !!qc[o]?.isDarkOverlay,
            c = () => e.jsx(hp, {
                fontWeight: "bold",
                variant: "h4",
                sx: {
                    fontFamily: 'Almarai, "Roboto", "Helvetica", "Arial"',
                    display: "inline-block"
                },
                component: "span",
                color: d ? "white" : "text.primary",
                children: "اللّه"
            });
        return e.jsx(E0, {
            LogoNavigateTo: "/",
            children: e.jsx(sP, {
                justifyContent: "center",
                spacing: 4,
                children: e.jsx(lP, {
                    children: e.jsxs(cP, {
                        ycenter: !0,
                        column: !0,
                        children: [e.jsxs(hp, {
                            variant: "h4",
                            sx: {
                                display: "inline-block"
                            },
                            children: [e.jsx(hp, {
                                variant: "h4",
                                fontWeight: "bold",
                                mr: 2,
                                component: "span",
                                color: d ? "white" : "text.primary",
                                bold: !0,
                                sx: {
                                    display: "inline-block"
                                },
                                children: "ar" === i.language ? e.jsxs(hp, {
                                    fontWeight: "bold",
                                    component: "span",
                                    variant: "h4",
                                    color: d ? "white" : "text.primary",
                                    children: ["حياك ", e.jsx(c, {}), " في", " "]
                                }) : a("welcome-to")
                            }), e.jsxs(hp, {
                                variant: "h4",
                                fontWeight: "bold",
                                component: "span",
                                primary: !0,
                                sx: {
                                    display: "inline-block",
                                    mx: 2,
                                    color: d ? "white" : "primary.main"
                                },
                                children: [" ", a(o)]
                            })]
                        }), e.jsx(hp, {
                            color: d ? "white" : "textSecondary",
                            variant: "body",
                            sx: {
                                fontWeight: 400,
                                letterSpacing: "0.3px",
                                textAlign: "center",
                                mb: 2,
                                mt: 2
                            },
                            children: a("signIn.signIn")
                        }), e.jsxs("form", {
                            onSubmit: t,
                            children: [qc[o]?.tenantName ? null : e.jsx(cP, {
                                sx: {
                                    my: [3]
                                },
                                children: e.jsx(v0, {
                                    form: n,
                                    companyId: "business_name",
                                    rules: {
                                        required: !0,
                                        pattern: /[a-zA-Zء-ي]/
                                    }
                                })
                            }), e.jsx(cP, {
                                sx: {
                                    width: {
                                        xs: "100%",
                                        md: "400px"
                                    },
                                    mt: "14px",
                                    "& #phone_country_code .MuiTypography-root": {
                                        fontSize: "inherit"
                                    }
                                },
                                children: e.jsx(x0, {
                                    form: n,
                                    phoneCountryCodeName: "phone_country_code",
                                    phoneNumberName: "phone_number",
                                    rules: {
                                        required: !0
                                    },
                                    isObject: !0,
                                    isDark: d,
                                    columnSizes: {
                                        codeField: 4.5,
                                        phoneField: 7.5
                                    }
                                })
                            }), !1, e.jsx(cP, {
                                sx: {
                                    my: [8]
                                },
                                children: e.jsx(a$, {
                                    size: "large",
                                    fullWidth: !0,
                                    name: f0,
                                    onClick: () => {
                                        t()
                                    },
                                    sx: {
                                        fontWeight: "bold",
                                        boxShadow: d ? "none" : "0px 0px 10px 0px rgba(0, 0, 0, 0.1)"
                                    },
                                    children: a("signIn.signIn")
                                })
                            }), qc[o]?.tenantName ? null : e.jsxs(sP, {
                                alignItems: "center",
                                justifyContent: "center",
                                column: !0,
                                children: [e.jsxs(hp, {
                                    variant: "subtitle1",
                                    component: "span",
                                    color: "text.primary",
                                    sx: {
                                        fontWeight: 400
                                    },
                                    children: [a("signIn.createAccount"), e.jsx(wp, {
                                        to: "/pricing",
                                        variant: "text",
                                        component: Wt,
                                        children: a("signIn.signUp")
                                    })]
                                }), false]
                            })]
                        })]
                    })
                })
            })
        })
    },
    I0 = {
        code: ""
    },
    F0 = ({
        children: e
    }) => {
        const t = bf({
                defaultValues: I0
            }),
            {
                fetchProfile: n,
                setToken: r
            } = Qc(),
            a = Ft(),
            i = Dt.createRef(),
            o = Ht();
        return e({
            form: t,
            handleVerify: async (e, r) => {
                const i = t.getValues(),
                    {
                        from: s,
                        ...l
                    } = o.state;
                try {
                    const {
                        data: e,
                        success: t
                    } = await (async e => await co("/tenancy/login", e))({
                        ...i,
                        ...l,
                        g_token: r,
                        fcm_token: localStorage.getItem($O) ?? ""
                    });
                    t && n(e.token)
                } catch (d) {
                    if (422 === d?.response?.status && d?.response?.data?.errors?.not_aproved) return void a("/401", {
                        replace: !0
                    });
                    Lo(d, {
                        setError: t.setError
                    })
                }
            },
            recaptchaRef: i
        })
    };
var H0, N0 = {};
var R0 = (H0 || (H0 = 1, function(e) {
    Object.defineProperty(e, "__esModule", {
        value: !0
    }), e.default = void 0;
    var t = function(e, t) {
            if (e && e.__esModule) return e;
            if (null === e || "object" !== m(e) && "function" != typeof e) return {
                default: e
            };
            var n = r(t);
            if (n && n.has(e)) return n.get(e);
            var a = {},
                i = Object.defineProperty && Object.getOwnPropertyDescriptor;
            for (var o in e)
                if ("default" !== o && Object.prototype.hasOwnProperty.call(e, o)) {
                    var s = i ? Object.getOwnPropertyDescriptor(e, o) : null;
                    s && (s.get || s.set) ? Object.defineProperty(a, o, s) : a[o] = e[o]
                } return a.default = e, n && n.set(e, a), a
        }(At()),
        n = ["placeholder", "separator", "isLastChild", "inputStyle", "focus", "isDisabled", "hasErrored", "errorStyle", "focusStyle", "disabledStyle", "shouldAutoFocus", "isInputNum", "index", "value", "className", "isInputSecure"];

    function r(e) {
        if ("function" != typeof WeakMap) return null;
        var t = new WeakMap,
            n = new WeakMap;
        return (r = function(e) {
            return e ? n : t
        })(e)
    }

    function a() {
        return a = Object.assign || function(e) {
            for (var t = 1; t < arguments.length; t++) {
                var n = arguments[t];
                for (var r in n) Object.prototype.hasOwnProperty.call(n, r) && (e[r] = n[r])
            }
            return e
        }, a.apply(this, arguments)
    }

    function i(e, t) {
        if (null == e) return {};
        var n, r, a = function(e, t) {
            if (null == e) return {};
            var n, r, a = {},
                i = Object.keys(e);
            for (r = 0; r < i.length; r++) n = i[r], t.indexOf(n) >= 0 || (a[n] = e[n]);
            return a
        }(e, t);
        if (Object.getOwnPropertySymbols) {
            var i = Object.getOwnPropertySymbols(e);
            for (r = 0; r < i.length; r++) n = i[r], t.indexOf(n) >= 0 || Object.prototype.propertyIsEnumerable.call(e, n) && (a[n] = e[n])
        }
        return a
    }

    function o(e, t) {
        if (!(e instanceof t)) throw new TypeError("Cannot call a class as a function")
    }

    function s(e, t, n) {
        return t && function(e, t) {
            for (var n = 0; n < t.length; n++) {
                var r = t[n];
                r.enumerable = r.enumerable || !1, r.configurable = !0, "value" in r && (r.writable = !0), Object.defineProperty(e, r.key, r)
            }
        }(e.prototype, t), e
    }

    function l(e, t) {
        if ("function" != typeof t && null !== t) throw new TypeError("Super expression must either be null or a function");
        e.prototype = Object.create(t && t.prototype, {
            constructor: {
                value: e,
                writable: !0,
                configurable: !0
            }
        }), t && d(e, t)
    }

    function d(e, t) {
        return d = Object.setPrototypeOf || function(e, t) {
            return e.__proto__ = t, e
        }, d(e, t)
    }

    function c(e) {
        var t = function() {
            if ("undefined" == typeof Reflect || !Reflect.construct) return !1;
            if (Reflect.construct.sham) return !1;
            if ("function" == typeof Proxy) return !0;
            try {
                return Boolean.prototype.valueOf.call(Reflect.construct(Boolean, [], function() {})), !0
            } catch (ti) {
                return !1
            }
        }();
        return function() {
            var n, r = p(e);
            if (t) {
                var a = p(this).constructor;
                n = Reflect.construct(r, arguments, a)
            } else n = r.apply(this, arguments);
            return function(e, t) {
                return !t || "object" !== m(t) && "function" != typeof t ? u(e) : t
            }(this, n)
        }
    }

    function u(e) {
        if (void 0 === e) throw new ReferenceError("this hasn't been initialised - super() hasn't been called");
        return e
    }

    function p(e) {
        return p = Object.setPrototypeOf ? Object.getPrototypeOf : function(e) {
            return e.__proto__ || Object.getPrototypeOf(e)
        }, p(e)
    }

    function h(e, t, n) {
        return t in e ? Object.defineProperty(e, t, {
            value: n,
            enumerable: !0,
            configurable: !0,
            writable: !0
        }) : e[t] = n, e
    }

    function m(e) {
        return m = "function" == typeof Symbol && "symbol" == typeof Symbol.iterator ? function(e) {
            return typeof e
        } : function(e) {
            return e && "function" == typeof Symbol && e.constructor === Symbol && e !== Symbol.prototype ? "symbol" : typeof e
        }, m(e)
    }
    var f = function(e) {
            return "object" === m(e)
        },
        g = function(e) {
            l(d, e);
            var r = c(d);

            function d(e) {
                var n;
                return o(this, d), h(u(n = r.call(this, e)), "getClasses", function() {
                    for (var e = arguments.length, t = new Array(e), n = 0; n < e; n++) t[n] = arguments[n];
                    return t.filter(function(e) {
                        return !f(e) && !1 !== e
                    }).join(" ")
                }), h(u(n), "getType", function() {
                    var e = n.props,
                        t = e.isInputSecure,
                        r = e.isInputNum;
                    return t ? "password" : r ? "tel" : "text"
                }), n.input = t.default.createRef(), n
            }
            return s(d, [{
                key: "componentDidMount",
                value: function() {
                    var e = this.props,
                        t = e.focus,
                        n = e.shouldAutoFocus,
                        r = this.input.current;
                    r && t && n && r.focus()
                }
            }, {
                key: "componentDidUpdate",
                value: function(e) {
                    var t = this.props.focus,
                        n = this.input.current;
                    e.focus !== t && n && t && (n.focus(), n.select())
                }
            }, {
                key: "render",
                value: function() {
                    var e = this.props,
                        r = e.placeholder,
                        o = e.separator,
                        s = e.isLastChild,
                        l = e.inputStyle,
                        d = e.focus,
                        c = e.isDisabled,
                        u = e.hasErrored,
                        p = e.errorStyle,
                        h = e.focusStyle,
                        m = e.disabledStyle;
                    e.shouldAutoFocus;
                    var g = e.isInputNum,
                        y = e.index,
                        v = e.value,
                        _ = e.className;
                    e.isInputSecure;
                    var x = i(e, n);
                    return t.default.createElement("div", {
                        className: _,
                        style: {
                            display: "flex",
                            alignItems: "center"
                        }
                    }, t.default.createElement("input", a({
                        "aria-label": "".concat(0 === y ? "Please enter verification code. " : "").concat(g ? "Digit" : "Character", " ").concat(y + 1),
                        autoComplete: "off",
                        style: Object.assign({
                            width: "1em",
                            textAlign: "center"
                        }, f(l) && l, d && f(h) && h, c && f(m) && m, u && f(p) && p),
                        placeholder: r,
                        className: this.getClasses(l, d && h, c && m, u && p),
                        type: this.getType(),
                        maxLength: "1",
                        ref: this.input,
                        disabled: c,
                        value: v || ""
                    }, x)), !s && o)
                }
            }]), d
        }(t.PureComponent),
        y = function(e) {
            l(r, e);
            var n = c(r);

            function r() {
                var e;
                o(this, r);
                for (var a = arguments.length, i = new Array(a), s = 0; s < a; s++) i[s] = arguments[s];
                return h(u(e = n.call.apply(n, [this].concat(i))), "state", {
                    activeInput: 0
                }), h(u(e), "getOtpValue", function() {
                    return e.props.value ? e.props.value.toString().split("") : []
                }), h(u(e), "getPlaceholderValue", function() {
                    var t = e.props,
                        n = t.placeholder,
                        r = t.numInputs;
                    if ("string" == typeof n) {
                        if (n.length === r) return n;
                        n.length
                    }
                }), h(u(e), "handleOtpChange", function(t) {
                    (0, e.props.onChange)(t.join(""))
                }), h(u(e), "isInputValueValid", function(t) {
                    return (e.props.isInputNum ? !isNaN(parseInt(t, 10)) : "string" == typeof t) && 1 === t.trim().length
                }), h(u(e), "focusInput", function(t) {
                    var n = e.props.numInputs,
                        r = Math.max(Math.min(n - 1, t), 0);
                    e.setState({
                        activeInput: r
                    })
                }), h(u(e), "focusNextInput", function() {
                    var t = e.state.activeInput;
                    e.focusInput(t + 1)
                }), h(u(e), "focusPrevInput", function() {
                    var t = e.state.activeInput;
                    e.focusInput(t - 1)
                }), h(u(e), "changeCodeAtFocus", function(t) {
                    var n = e.state.activeInput,
                        r = e.getOtpValue();
                    r[n] = t[0], e.handleOtpChange(r)
                }), h(u(e), "handleOnPaste", function(t) {
                    t.preventDefault();
                    var n = e.state.activeInput,
                        r = e.props,
                        a = r.numInputs;
                    if (!r.isDisabled) {
                        for (var i = e.getOtpValue(), o = n, s = t.clipboardData.getData("text/plain").slice(0, a - n).split(""), l = 0; l < a; ++l) l >= n && s.length > 0 && (i[l] = s.shift(), o++);
                        e.setState({
                            activeInput: o
                        }, function() {
                            e.focusInput(o), e.handleOtpChange(i)
                        })
                    }
                }), h(u(e), "handleOnChange", function(t) {
                    var n = t.target.value;
                    e.isInputValueValid(n) && e.changeCodeAtFocus(n)
                }), h(u(e), "handleOnKeyDown", function(t) {
                    8 === t.keyCode || "Backspace" === t.key ? (t.preventDefault(), e.changeCodeAtFocus(""), e.focusPrevInput()) : 46 === t.keyCode || "Delete" === t.key ? (t.preventDefault(), e.changeCodeAtFocus("")) : 37 === t.keyCode || "ArrowLeft" === t.key ? (t.preventDefault(), e.focusPrevInput()) : 39 === t.keyCode || "ArrowRight" === t.key ? (t.preventDefault(), e.focusNextInput()) : 32 !== t.keyCode && " " !== t.key && "Spacebar" !== t.key && "Space" !== t.key || t.preventDefault()
                }), h(u(e), "handleOnInput", function(t) {
                    if (e.isInputValueValid(t.target.value)) e.focusNextInput();
                    else if (!e.props.isInputNum) {
                        var n = t.nativeEvent;
                        null === n.data && "deleteContentBackward" === n.inputType && (t.preventDefault(), e.changeCodeAtFocus(""), e.focusPrevInput())
                    }
                }), h(u(e), "renderInputs", function() {
                    for (var n = e.state.activeInput, r = e.props, a = r.numInputs, i = r.inputStyle, o = r.focusStyle, s = r.separator, l = r.isDisabled, d = r.disabledStyle, c = r.hasErrored, u = r.errorStyle, p = r.shouldAutoFocus, h = r.isInputNum, m = r.isInputSecure, f = r.className, y = [], v = e.getOtpValue(), _ = e.getPlaceholderValue(), x = e.props["data-cy"], b = e.props["data-testid"], w = function(r) {
                            y.push(t.default.createElement(g, {
                                placeholder: _ && _[r],
                                key: r,
                                index: r,
                                focus: n === r,
                                value: v && v[r],
                                onChange: e.handleOnChange,
                                onKeyDown: e.handleOnKeyDown,
                                onInput: e.handleOnInput,
                                onPaste: e.handleOnPaste,
                                onFocus: function(t) {
                                    e.setState({
                                        activeInput: r
                                    }), t.target.select()
                                },
                                onBlur: function() {
                                    return e.setState({
                                        activeInput: -1
                                    })
                                },
                                separator: s,
                                inputStyle: i,
                                focusStyle: o,
                                isLastChild: r === a - 1,
                                isDisabled: l,
                                disabledStyle: d,
                                hasErrored: c,
                                errorStyle: u,
                                shouldAutoFocus: p,
                                isInputNum: h,
                                isInputSecure: m,
                                className: f,
                                "data-cy": x && "".concat(x, "-").concat(r),
                                "data-testid": b && "".concat(b, "-").concat(r)
                            }))
                        }, C = 0; C < a; C++) w(C);
                    return y
                }), e
            }
            return s(r, [{
                key: "render",
                value: function() {
                    var e = this.props.containerStyle;
                    return t.default.createElement("div", {
                        style: Object.assign({
                            display: "flex"
                        }, f(e) && e),
                        className: f(e) ? "" : e
                    }, this.renderInputs())
                }
            }]), r
        }(t.Component);
    h(y, "defaultProps", {
        numInputs: 4,
        onChange: function(e) {},
        isDisabled: !1,
        shouldAutoFocus: !1,
        value: "",
        isInputSecure: !1
    });
    var v = y;
    e.default = v
}(N0)), N0);
const Y0 = It(R0);

function B0({
    onVerify: t,
    form: {
        formState: n,
        register: r,
        setValue: a,
        getValues: i,
        reset: o
    },
    recaptchaRef: s,
    onresendOTP: l
}) {
    const d = Ht(),
        [u, p] = Dt.useState(""),
        [h, m] = Dt.useState(!1),
        [f, g] = Dt.useState(0),
        {
            t: y,
            i18n: v
        } = Gn(),
        {
            minutes: _,
            seconds: x,
            setMinutes: b,
            setSeconds: w
        } = (() => {
            const [e, t] = Dt.useState(0), [n, r] = Dt.useState(60), a = t => {
                t.preventDefault(), localStorage.setItem(qO, JSON.stringify({
                    minutes: e,
                    seconds: n
                }))
            };
            return Dt.useEffect(() => {
                const e = JSON.parse(localStorage.getItem(qO));
                if (e) {
                    const {
                        minutes: n,
                        seconds: a
                    } = e;
                    r(a), t(n)
                }
            }, []), Dt.useEffect(() => {
                window.addEventListener("beforeunload", a);
                let i = setInterval(() => {
                    n > 0 && r(n - 1), 0 === n && (0 === e ? clearInterval(i) : (t(e - 1), r(59)))
                }, 1e3);
                return () => {
                    clearInterval(i), window.removeEventListener("beforeunload", a)
                }
            }, [n, e]), {
                seconds: n,
                minutes: e,
                setMinutes: t,
                setSeconds: r
            }
        })(),
        {
            CurrentBrand: C
        } = Gc();
    Dt.useEffect(() => () => {
        localStorage.removeItem(qO)
    }, []);
    const M = Ft();
    Dt.useEffect(() => {
        d?.state?.phone_number || (localStorage.removeItem(qO), M("/login"))
    }, []);
    const S = async e => {
        try {
            const {
                data: t
            } = await l(e);
            t && (b(0), w(60), localStorage.removeItem(qO))
        } catch (t) {
            Lo(t, {
                setError: void 0
            }, !0)
        }
    }, L = () => {
        g(e => e + 1)
    }, k = () => {
        h ? ((e = null) => {
            let t = d?.state;
            S({
                phone_country_code: t?.phone_country_code,
                phone_number: t?.phone_number,
                business_name: t?.business_name,
                g_token: t?.g_token || e
            }), m(!1), o()
        })() : t(L)
    }, T = d?.state?.phone_country_code?.name || "", j = !!qc[C]?.isDarkOverlay, E = c();
    return e.jsx(E0, {
        children: e.jsx(sP, {
            justifyContent: "center",
            spacing: 4,
            children: e.jsxs(lP, {
                xs: 10,
                sm: 10,
                md: 3,
                lg: 3,
                align: "center",
                children: [!1, e.jsx(rP, {
                    variant: "h4",
                    component: "span",
                    className: "mt-3",
                    color: j ? "white" : "text.primary",
                    children: y("verifyPhone.title")
                }), e.jsx(rP, {
                    s: 14,
                    sx: {
                        mt: 8,
                        fontWeight: 300
                    },
                    variant: "subtitle1",
                    color: j ? "white" : "textSecondary",
                    children: y("verifyPhone.message")
                }), e.jsx(sP, {
                    alignItems: "center",
                    justifyContent: "center",
                    sx: {
                        gap: 3
                    },
                    children: e.jsx(rP, {
                        variant: "subtitle1",
                        sx: {},
                        color: j ? "white" : "text.primary",
                        children: T.replace(/[^0-9]/g, "").concat(d?.state?.phone_number)
                    })
                }), e.jsxs("form", {
                    style: {
                        direction: "ltr"
                    },
                    children: [(0 !== _ || 0 !== x) && e.jsxs(e.Fragment, {
                        children: [e.jsx(i$, {
                            ...r("code", {
                                required: !0
                            }),
                            label: y("verifyPhone.code"),
                            isDark: j,
                            errors: n.errors,
                            hidden: !0,
                            style: {
                                display: "none"
                            }
                        }), e.jsx(Y0, {
                            containerStyle: {
                                justifyContent: "center"
                            },
                            value: u,
                            onChange: e => {
                                return p(t = e), a("code", t), i(), void(4 === t.length && k());
                                var t
                            },
                            hasErrored: !!n.errors.code,
                            errorStyle: z0.otpError,
                            numInputs: 4,
                            isInputNum: !0,
                            inputStyle: {
                                textAlign: "center",
                                backgroundColor: E?.palette?.background?.default,
                                color: E?.palette?.text?.primary,
                                borderRadius: "11px",
                                height: "40px",
                                width: "40px",
                                margin: "12px",
                                border: `1px solid ${E?.palette?.divider}`,
                                "&.focus-visible": {
                                    border: "none"
                                },
                                "&:focus": {
                                    border: `2px solid ${E?.palette?.primary?.main} !important`
                                }
                            },
                            shouldAutoFocus: !0,
                            separator: e.jsx("span", {
                                children: " "
                            })
                        }), n?.errors?.code && e.jsx("div", {
                            style: z0.errorMessage,
                            children: n?.errors?.code?.message
                        }), e.jsxs(rP, {
                            variant: "caption",
                            sx: {
                                fontSize: "1.2rem",
                                fontWeight: "normal",
                                color: j ? "white" : "textSecondary"
                            },
                            children: [y("verifyPhone.newCode"), " ", e.jsxs(rP, {
                                variant: "caption",
                                sx: {
                                    fontWeight: "bold",
                                    ...j ? {
                                        color: "#fff"
                                    } : {}
                                },
                                children: [_ < 10 ? `0${_}` : _, ":", x < 10 ? `0${x}` : x]
                            })]
                        })]
                    }), e.jsx(cP, {
                        sx: {
                            textAlign: "center",
                            mt: 8
                        },
                        children: 0 === _ && 0 === x && e.jsx(dP, {
                            variant: "contained",
                            color: "primary",
                            onClick: () => {
                                m(!0), l({
                                    phone_country_code: d?.state?.phone_country_code,
                                    phone_number: d?.state?.phone_number,
                                    business_name: d?.state?.business_name,
                                    privacy_policy_accept: !0
                                }).then(e => {
                                    e?.data && (b(0), w(60), localStorage.removeItem(qO), m(!1))
                                })
                            },
                            sx: {
                                width: "100%"
                            },
                            children: y("verifyPhone.resend")
                        })
                    })]
                })]
            })
        })
    })
}
const z0 = {
    otpError: {
        border: "1px solid red"
    },
    errorMessage: {
        color: "red",
        margin: "0px !important"
    }
};

function U0({
    handleClose: t,
    isOpen: n,
    user_id: r
}) {
    const {
        mutate: a,
        isLoading: i
    } = nl({
        mutationFn: () => (async e => {
            try {
                await lo(`/api-management/contacts/${e}/accept-privacy-policy`)
            } catch (t) {
                throw t
            }
        })(r),
        onSuccess: () => {
            t()
        }
    }), {
        t: o
    } = Gn();
    return e.jsxs(v, {
        onClose: t,
        open: n,
        fullWidth: !0,
        maxWidth: "xl",
        children: [e.jsx(b, {
            children: o("popup.privacyPolicy")
        }), e.jsx(_, {
            children: e.jsxs(x, {
                children: [e.jsx(Ne, {
                    children: e.jsx("iframe", {
                        src: "https:/.termly.io/document/terms-of-use-for-saas/420f723a-13d3-4a0e-8a96-dfffeea507f4",
                        width: "100%",
                        height: "700"
                    })
                }), e.jsx(wp, {
                    onClick: () => a(),
                    isLoading: i,
                    children: o("dashboard.acceptPrivacyPolicy")
                })]
            })
        })]
    })
}
const W0 = {
        cutoffDate: "2026-2-1T00:00:00Z",
        version: 5,
        date: {
            en: "Dec 28, 2025",
            ar: "28 ديسمبر، 2025"
        },
        sections: [{
            title: {
                en: "Off-Plan Sales Solution",
                ar: "نقلة نوعية في البيع العقاري داخل منصة واحدة"
            },
            intro: [{
                en: "In this update, we launch a fully integrated and automated Off-Plan Sales solution within our platform.",
                ar: "في هذا التحديث، قمنا بإطلاق نظام متكامل ومؤتمت للبيع على الخارطة داخل منصتنا."
            }, {
                en: 'The system now brings together "Ready Unit Sales" and "Off-Plan Sales" in one seamless experience, designed to support your business growth with confidence and efficiency.',
                ar: "اليوم، يجمع النظام بين بيع الوحدات الجاهزة والبيع على الخارطة في تجربة موحدة وسلسة، مصممة لدعم نمو أعمالكم بثقة وكفاءة."
            }],
            subsectionTitle: {
                en: "What does this update offer?",
                ar: "ماذا يقدّم لكم هذا التحديث؟"
            },
            items: [{
                en: "Easily list off-plan communities from a single place",
                ar: "إدراج المشاريع للبيع على الخارطة بسهولة ومن مكان واحد."
            }, {
                en: "Manage Wafi license details to ensure full regulatory compliance",
                ar: "إدارة رخص وافي لضمان الامتثال الكامل للأنظمة."
            }, {
                en: "Link communities to their escrow (guarantee) accounts with transparency and trust",
                ar: "ربط المشاريع بـ حسابات الضمان بكل موثوقية وشفافية."
            }, {
                en: "Create smart, milestone-based payment schedules aligned with project completion",
                ar: "إنشاء جداول دفعات ذكية تعتمد على نسب إنجاز المشروع."
            }, {
                en: "Generate SADAD numbers instantly with one click for eligible customers",
                ar: "إصدار أرقام سداد فورًا وبضغطة زر لجميع العملاء المؤهلين."
            }, {
                en: "Automated payment reminders to improve collection and reduce delays",
                ar: "تذكيرات تلقائية تضمن متابعة التحصيل وتقليل التأخير في السداد."
            }],
            closing: {
                en: "This update empowers you to manage off-plan sales with greater efficiency, tighter control, and a more professional experience from project listing to final payment collection.",
                ar: "هذا التحديث يمكّنكم من إدارة عمليات البيع على الخارطة بكفاءة أعلى، تحكم أدق، وتجربة أكثر احترافية من لحظة إدراج المشروع وحتى تحصيل آخر دفعة."
            }
        }]
    },
    Z0 = `new_features_shown_v_f${W0.version}`,
    q0 = () => {
        const t = s(),
            {
                CurrentBrand: n
            } = Gc(),
            {
                i18n: r,
                t: i
            } = Gn(),
            [o, l] = Dt.useState(!1),
            d = "ar" === r.language,
            c = "ar" === r.language ? "ar" : "en";
        Dt.useEffect(() => {
            const e = localStorage.getItem(Z0);
            if (new Date < new Date(W0.cutoffDate) && !e) {
                const e = setTimeout(() => l(!0), 1e3);
                return () => clearTimeout(e)
            }
        }, []);
        const p = () => {
            l(!1), localStorage.setItem(Z0, "true")
        };
        return e.jsx(v, {
            open: o,
            onClose: p,
            sx: {
                "& .MuiDialog-paper": {
                    maxWidth: "720px !important",
                    minWidth: "540px !important",
                    direction: d ? "rtl" : "ltr"
                }
            },
            PaperProps: {
                sx: {
                    borderRadius: "24px !important",
                    overflow: "hidden"
                }
            },
            children: e.jsxs(_, {
                sx: {
                    p: 0,
                    position: "relative"
                },
                children: [e.jsx(a, {
                    sx: {
                        height: 120,
                        background: `linear-gradient(135deg, ${t.palette.primary.main} 0%, ${u(t.palette.primary.light,.8)} 100%)`,
                        borderTopLeftRadius: "8px",
                        borderTopRightRadius: "8px",
                        position: "relative"
                    },
                    children: e.jsx(w, {
                        onClick: p,
                        sx: {
                            position: "absolute",
                            top: 12,
                            right: 12,
                            color: "white",
                            bgcolor: u(t.palette.common.black, .2),
                            "&:hover": {
                                bgcolor: u(t.palette.common.black, .4)
                            }
                        },
                        children: e.jsx(dt, {})
                    })
                }), e.jsxs(me, {
                    elevation: 8,
                    sx: {
                        m: "0 auto",
                        mt: -14,
                        mb: "30px",
                        borderRadius: "12px !important",
                        p: 3,
                        pt: 2,
                        mx: "30px",
                        position: "relative",
                        zIndex: 1,
                        boxShadow: "0 4px 24px 0 rgba(0,0,0,0.10), 0 1.5px 6px 0 rgba(0,0,0,0.06)"
                    },
                    children: [e.jsxs(a, {
                        sx: {
                            textAlign: "center",
                            mb: 2
                        },
                        children: [qc[n]?.loadingLogo ? e.jsx("img", {
                            src: qc[n].loadingLogo,
                            alt: "Logo",
                            style: {
                                height: 85,
                                maxWidth: 250,
                                objectFit: "contain",
                                margin: "16px auto"
                            }
                        }) : null, e.jsx(hp, {
                            variant: "h5",
                            fontWeight: "700",
                            children: i("whatsNew")
                        }), e.jsxs(hp, {
                            variant: "h6",
                            color: "text.secondary",
                            mb: "12px",
                            children: [i("releasedOn"), " ", W0.date[c]]
                        })]
                    }), e.jsx(L, {
                        sx: {
                            my: "24px"
                        }
                    }), e.jsx(a, {
                        sx: {
                            maxHeight: "calc(100vh - 450px)",
                            overflowY: "auto",
                            px: "8px",
                            py: 1
                        },
                        children: W0.sections.map((t, n) => e.jsxs(a, {
                            mb: 3,
                            children: [e.jsx(a, {
                                display: "flex",
                                alignItems: "center",
                                gap: "8px",
                                mb: "8px",