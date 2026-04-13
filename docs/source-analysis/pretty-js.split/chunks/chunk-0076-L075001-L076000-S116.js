                if (r) return n([{
                    id: Math.random(),
                    file: e.target.files[0],
                    state: QZ.PENDING
                }]);
                const a = [...t || [], ...Array.from(e.target.files) || []].reduce((e, t) => ({
                        ...e,
                        [Tq(t) ? "images" : "files"]: Tq(t) ? e.images + 1 : e.files + 1
                    }), {
                        images: 0,
                        files: 0
                    }),
                    {
                        maxImagesLength: i = 5,
                        maxFilesLength: o = 1
                    } = s;
                if (!i && a.images) return Zi.warning("User cannot upload images.");
                if (!o && a.files) return Zi.warning("User cannot upload files.");
                if (i && a.images > i) return Zi.warning(`User can only upload ${i} images.`);
                if (o && a.files > o) return Zi.warning(`User can only upload ${o} files.`);
                const l = [...t],
                    d = e.target.files;
                for (var c = 0; c < d.length; c++) l.push({
                    id: uuidv4(),
                    file: d[c],
                    state: QZ.PENDING
                });
                return n(l)
            },
            name: o,
            options: s
        }), e.jsx(Sq, {
            files: t,
            onDeleteFile: e => {
                const r = t.findIndex(t => t.id === e);
                if (-1 === r) return;
                const a = [...t];
                if (a[r].file) return a.splice(r, 1), void n(a);
                a[r].state = QZ.DELETING, n(a), i(a[r].id)
            },
            singleFile: r
        })]
    })
}
let Eq = 0;
const Dq = {},
    Vq = Symbol(),
    Aq = e => !!e[Vq],
    Oq = e => !e[Vq].c,
    Pq = e => {
        var t;
        const {
            b: n,
            c: r
        } = e[Vq];
        r && (r(), null == (t = Hq.get(n)) || t())
    },
    Iq = (e, t) => {
        const n = e[Vq].o,
            r = t[Vq].o;
        return n === r || e === r || Aq(n) && Iq(n, t)
    },
    Fq = (e, t) => {
        const n = {
                b: e,
                o: t,
                c: null
            },
            r = new Promise(e => {
                n.c = () => {
                    n.c = null, e()
                }, t.finally(n.c)
            });
        return r[Vq] = n, r
    },
    Hq = new WeakMap,
    Nq = e => "init" in e,
    Rq = "r",
    Yq = "w",
    Bq = "c",
    zq = "s",
    Uq = "h",
    Wq = "n",
    Zq = "l",
    qq = "a",
    $q = "m",
    Gq = e => {
        const t = new WeakMap,
            n = new WeakMap,
            r = new Map;
        let a, i;
        "production" !== (Dq && "production") && (a = new Set, i = new Set);
        const o = new WeakMap,
            s = new WeakMap,
            l = e => {
                let t = s.get(e);
                return t || (t = new Map, s.set(e, t)), t
            },
            d = (e, n) => {
                if (e) {
                    const t = l(e);
                    let r = t.get(n);
                    return r || (r = d(e.p, n), r && "p" in r && Oq(r.p) && (r = void 0), r && t.set(n, r)), r
                }
                return t.get(n)
            },
            c = (e, n, a) => {
                if ("production" !== (Dq && "production") && Object.freeze(a), e) {
                    l(e).set(n, a)
                } else {
                    const e = t.get(n);
                    t.set(n, a), r.has(n) || r.set(n, e)
                }
            },
            u = (e, t = new Map, n) => {
                if (!n) return t;
                const r = new Map;
                let a = !1;
                return n.forEach(n => {
                    var i;
                    const o = (null == (i = d(e, n)) ? void 0 : i.r) || 0;
                    r.set(n, o), t.get(n) !== o && (a = !0)
                }), t.size !== r.size || a ? r : t
            },
            p = (e, t, n, r, a) => {
                const i = d(e, t);
                if (i) {
                    if (a && (!("p" in i) || !Iq(i.p, a))) return i;
                    "p" in i && Pq(i.p)
                }
                const o = {
                    v: n,
                    r: (null == i ? void 0 : i.r) || 0,
                    y: !0,
                    d: u(e, null == i ? void 0 : i.d, r)
                };
                let s = !(null == i ? void 0 : i.y);
                return i && "v" in i && Object.is(i.v, n) ? o.d === i.d || o.d.size === i.d.size && Array.from(o.d.keys()).every(e => i.d.has(e)) || (s = !0, Promise.resolve().then(() => {
                    S(e)
                })) : (s = !0, ++o.r, o.d.has(t) && (o.d = new Map(o.d).set(t, o.r))), i && !s ? i : (c(e, t, o), o)
            },
            h = (e, t, n, r, a) => {
                const i = d(e, t);
                if (i) {
                    if (a && (!("p" in i) || !Iq(i.p, a))) return i;
                    "p" in i && Pq(i.p)
                }
                const o = {
                    e: n,
                    r: ((null == i ? void 0 : i.r) || 0) + 1,
                    y: !0,
                    d: u(e, null == i ? void 0 : i.d, r)
                };
                return c(e, t, o), o
            },
            m = (e, t, n, r) => {
                const a = d(e, t);
                if (a && "p" in a) {
                    if (Iq(a.p, n) && !Oq(a.p)) return a.y ? a : {
                        ...a,
                        y: !0
                    };
                    Pq(a.p)
                }((e, t, n) => {
                    let r = o.get(t);
                    r || (r = new Map, o.set(t, r)), n.then(() => {
                        r.get(e) === n && (r.delete(e), r.size || o.delete(t))
                    }), r.set(e, n)
                })(e, t, n);
                const i = {
                    p: n,
                    r: ((null == a ? void 0 : a.r) || 0) + 1,
                    y: !0,
                    d: u(e, null == a ? void 0 : a.d, r)
                };
                return c(e, t, i), i
            },
            f = (e, t, n, r) => {
                if (n instanceof Promise) {
                    const a = Fq(n, n.then(n => {
                        p(e, t, n, r, a)
                    }).catch(n => {
                        if (n instanceof Promise) return Aq(n) ? n.then(() => {
                            g(e, t, !0)
                        }) : n;
                        h(e, t, n, r, a)
                    }));
                    return m(e, t, a, r)
                }
                return p(e, t, n, r)
            },
            g = (e, t, r) => {
                if (!r) {
                    const r = d(e, t);
                    if (r) {
                        if (r.y && "p" in r && !Oq(r.p)) return r;
                        if (r.d.forEach((r, a) => {
                                if (a !== t)
                                    if (n.has(a)) {
                                        const t = d(e, a);
                                        t && !t.y && g(e, a)
                                    } else g(e, a)
                            }), Array.from(r.d).every(([t, n]) => {
                                const r = d(e, t);
                                return r && !("p" in r) && r.r === n
                            })) return r.y ? r : {
                            ...r,
                            y: !0
                        }
                    }
                }
                const a = new Set;
                try {
                    const n = t.read(n => {
                        a.add(n);
                        const r = n === t ? d(e, n) : g(e, n);
                        if (r) {
                            if ("e" in r) throw r.e;
                            if ("p" in r) throw r.p;
                            return r.v
                        }
                        if (Nq(n)) return n.init;
                        throw new Error("no atom init")
                    });
                    return f(e, t, n, a)
                } catch (i) {
                    if (i instanceof Promise) {
                        const n = Aq(i) && Oq(i) ? (e => Fq(e[Vq].b, e[Vq].o))(i) : Fq(i, i);
                        return m(e, t, n, a)
                    }
                    return h(e, t, i, a)
                }
            },
            y = (e, t) => g(t, e),
            v = (e, t) => !t.l.size && (!t.t.size || 1 === t.t.size && t.t.has(e)),
            _ = (e, t) => {
                const r = n.get(t);
                null == r || r.t.forEach(n => {
                    n !== t && (((e, t) => {
                        const n = d(e, t);
                        if (n) {
                            const r = {
                                ...n,
                                y: !1
                            };
                            c(e, t, r)
                        }
                    })(e, n), _(e, n))
                })
            },
            x = (e, t, n) => {
                let r = !0;
                const a = (t, n) => {
                        const r = g(e, t);
                        if ("e" in r) throw r.e;
                        if ("p" in r) {
                            if (null == n ? void 0 : n.unstable_promise) return r.p.then(() => {
                                const i = d(e, t);
                                return i && "p" in i && i.p === r.p ? new Promise(e => setTimeout(e)).then(() => a(t, n)) : a(t, n)
                            });
                            throw r.p
                        }
                        if ("v" in r) return r.v;
                        throw new Error("no value found")
                    },
                    i = t.write(a, (n, a) => {
                        let i;
                        if (n === t) {
                            if (!Nq(n)) throw new Error("atom not writable");
                            const t = (e => {
                                const t = new Set,
                                    n = o.get(e);
                                return n && (o.delete(e), n.forEach((e, n) => {
                                    Pq(e), t.add(n)
                                })), t
                            })(n);
                            t.forEach(t => {
                                t !== e && f(t, n, a)
                            });
                            d(e, n) !== f(e, n, a) && _(e, n)
                        } else i = x(e, n, a);
                        return r || S(e), i
                    }, n);
                return r = !1, i
            },
            b = (e, t, n) => {
                const r = x(n, e, t);
                return S(n), r
            },
            w = (e, t, r) => {
                const a = {
                    t: new Set(r && [r]),
                    l: new Set
                };
                n.set(t, a), "production" !== (Dq && "production") && i.add(t);
                if (g(void 0, t).d.forEach((r, a) => {
                        const i = n.get(a);
                        i ? i.t.add(t) : a !== t && w(e, a, t)
                    }), (e => !!e.write)(t) && t.onMount) {
                    const n = n => b(t, n, e),
                        r = t.onMount(n);
                    e = void 0, r && (a.u = r)
                }
                return a
            },
            C = (e, t) => {
                var r;
                const a = null == (r = n.get(t)) ? void 0 : r.u;
                a && a(), n.delete(t), "production" !== (Dq && "production") && i.delete(t);
                const o = d(e, t);
                o && ("p" in o && Pq(o.p), o.d.forEach((r, a) => {
                    if (a !== t) {
                        const r = n.get(a);
                        r && (r.t.delete(t), v(a, r) && C(e, a))
                    }
                }))
            },
            M = (e, t, r, a) => {
                const i = new Set(r.d.keys());
                null == a || a.forEach((r, a) => {
                    if (i.has(a)) return void i.delete(a);
                    const o = n.get(a);
                    o && (o.t.delete(t), v(a, o) && C(e, a))
                }), i.forEach(r => {
                    const a = n.get(r);
                    a ? a.t.add(t) : n.has(t) && w(e, r, t)
                })
            },
            S = e => {
                if (e) {
                    return void l(e).forEach((r, a) => {
                        if (r !== t.get(a)) {
                            const t = n.get(a);
                            null == t || t.l.forEach(t => t(e))
                        }
                    })
                }
                for (; r.size;) {
                    const e = Array.from(r);
                    r.clear(), e.forEach(([e, t]) => {
                        const r = d(void 0, e);
                        if (r && r.d !== (null == t ? void 0 : t.d) && M(void 0, e, r, null == t ? void 0 : t.d), t && !t.y && (null == r ? void 0 : r.y)) return;
                        const a = n.get(e);
                        null == a || a.l.forEach(e => e())
                    })
                }
                "production" !== (Dq && "production") && a.forEach(e => e())
            },
            L = (e, n) => {
                n && (e => {
                    l(e).forEach((n, r) => {
                        const a = t.get(r);
                        (!a || n.r > a.r || n.y !== a.y || n.r === a.r && n.d !== a.d) && (t.set(r, n), n.d !== (null == a ? void 0 : a.d) && M(e, r, n, null == a ? void 0 : a.d))
                    })
                })(n), S(void 0)
            },
            k = (e, t, r) => {
                const a = ((e, t) => {
                        let r = n.get(t);
                        return r || (r = w(e, t)), r
                    })(r, e),
                    i = a.l;
                return i.add(t), () => {
                    i.delete(t), ((e, t) => {
                        const r = n.get(t);
                        r && v(t, r) && C(e, t)
                    })(r, e)
                }
            },
            T = (e, t) => {
                for (const [n, r] of e) Nq(n) && (f(t, n, r), _(t, n));
                S(t)
            };
        return "production" !== (Dq && "production") ? {
            [Rq]: y,
            [Yq]: b,
            [Bq]: L,
            [zq]: k,
            [Uq]: T,
            [Wq]: e => (a.add(e), () => {
                a.delete(e)
            }),
            [Zq]: () => i.values(),
            [qq]: e => t.get(e),
            [$q]: e => n.get(e)
        } : {
            [Rq]: y,
            [Yq]: b,
            [Bq]: L,
            [zq]: k,
            [Uq]: T
        }
    },
    Kq = new Map,
    Qq = e => {
        var t, n;
        return Kq.has(e) || Kq.set(e, Dt.createContext({
            s: n ? n(t).SECRET_INTERNAL_store : Gq()
        })), Kq.get(e)
    };

function Jq(e, t) {
    return function(e) {
        const t = "atom" + ++Eq,
            n = {
                toString: () => t
            };
        return n.init = e, n.read = e => e(n), n.write = (e, t, r) => t(n, "function" == typeof r ? r(e(n)) : r), n
    }(e)
}

function Xq(e, t) {
    const n = Qq(t),
        r = Dt.useContext(n),
        {
            s: a,
            v: i
        } = r,
        o = t => {
            const n = a.r(e, t);
            if ("production" !== (Dq && "production") && !n.y) throw new Error("should not be invalidated");
            if ("e" in n) throw n.e;
            if ("p" in n) throw n.p;
            if ("v" in n) return n.v;
            throw new Error("no atom value")
        },
        [
            [s, l, d], c
        ] = Dt.useReducer((t, n) => {
            const r = o(n);
            return Object.is(t[1], r) && t[2] === e ? t : [n, r, e]
        }, i, t => [t, o(t), e]);
    let u = l;
    return d !== e && (c(s), u = o(s)), Dt.useEffect(() => {
        const {
            v: t
        } = r;
        t && a[Bq](e, t);
        const n = a.s(e, c, t);
        return c(t), n
    }, [a, e, r]), Dt.useEffect(() => {
        a[Bq](e, s)
    }), Dt.useDebugValue(u), u
}

function e$(e, t) {
    const n = Qq(t),
        {
            s: r,
            w: a
        } = Dt.useContext(n),
        i = Dt.useCallback(t => {
            if ("production" !== (Dq && "production") && !("write" in e)) throw new Error("not writable atom");
            const n = n => r.w(e, t, n);
            return a ? a(n) : n()
        }, [r, a, e]);
    return i
}

function t$(e, t) {
    return "scope" in e && (t = e.scope), [Xq(e, t), e$(e, t)]
}

function n$({
    initial: e = !1,
    name: t,
    children: n
}) {
    const [r, a] = Dt.useState(e);
    return Dt.useEffect(() => {
        var e, n;
        return e = t, n = a, mq.set(e, n), () => {
            ! function(e) {
                mq.delete(e)
            }(t)
        }
    }, []), n(r)
}

function r$({
    children: t,
    isLoading: n,
    startIcon: r,
    size: a = "large",
    ...i
}) {
    return e.jsx(l, {
        size: a,
        fullWidth: !0,
        color: "primary",
        variant: "contained",
        disableElevation: !0,
        disabled: n,
        startIcon: r,
        ...i,
        children: n ? e.jsx(d, {
            size: 26
        }) : t
    })
}

function a$({
    initial: t,
    name: n,
    ...r
}) {
    return e.jsx(n$, {
        initial: t,
        name: n,
        children: t => e.jsx(r$, {
            isLoading: t,
            ...r
        })
    })
}
const i$ = Dt.forwardRef(({
    errors: t,
    name: n,
    margin: r = "normal",
    label: a,
    labelSize: i = 14,
    isMsgDisabled: o,
    isDark: s = !1,
    limit: l,
    multiline: d,
    onChange: c,
    ...u
}, p) => {
    const [h, m] = Dt.useState(0);
    let f = null;
    const {
        i18n: g
    } = Gn();
    if (!o && t && n && bh.get(t, n)?.type) {
        const e = bh.get(t, n);
        f = e.message ? e?.message : "required" === e.type ? "en" === g.language ? "This is required" : "هذا مطلوب" : "en" === g.language ? "This is invalid" : "هذا غير صالح"
    }
    const y = d && void 0 !== l;
    return e.jsxs(cP, {
        column: !0,
        sx: {
            width: "100%"
        },
        children: [e.jsx(rP, {
            variant: "caption",
            sx: {
                mb: "-10px",
                fontWeight: 400,
                ...s && {
                    color: "#fff"
                }
            },
            s: i,
            children: a
        }), e.jsxs(cP, {
            sx: {
                position: "relative",
                width: "100%"
            },
            children: [e.jsx(E, {
                sx: {
                    background: "#fff",
                    width: "100%",
                    "& .MuiInputBase-root": {
                        width: "100%"
                    }
                },
                margin: r,
                name: n,
                error: !!f,
                helperText: f,
                inputRef: p,
                onChange: e => {
                    y && m(e.target.value.length), c && c(e)
                },
                multiline: d,
                ...u,
                hiddenLabel: !0,
                fullWidth: !0
            }), y && e.jsxs(rP, {
                variant: "caption",
                sx: {
                    position: "absolute",
                    bottom: f ? "40px" : "15px",
                    right: "8px",
                    fontSize: "12px",
                    color: h > l ? "#d32f2f" : "#666",
                    backgroundColor: "#fff",
                    padding: "0 4px",
                    zIndex: 1
                },
                children: [h, "/", l]
            })]
        })]
    })
});

function o$({
    name: t,
    control: n,
    rules: r,
    defaultValue: a = "",
    ref: i,
    margin: o = "normal",
    placeholder: s,
    isMsgDisabled: l = !1,
    ...d
}) {
    return e.jsx(Mm, {
        control: n,
        name: t,
        rules: r,
        render: ({
            field: n
        }) => e.jsx(i$, {
            isMsgDisabled: l,
            placeholder: s,
            sx: {
                "& .MuiInputBase-input": {
                    textAlign: "left",
                    backgroundColor: d?.disabled ? "#F0F0F0" : "transparent"
                },
                "& .MuiInputBase-root.Mui-focused": {
                    border: e => `1px solid ${e.palette.primary.main} !important`
                },
                "& .MuiInputBase-root fieldset": {
                    border: "none"
                },
                "& label+.Mui-disabled": {
                    backgroundColor: "#F0F0F0"
                },
                "& label+.Mui-disabled svg": {
                    display: "none"
                }
            },
            inputProps: {
                "aria-label": t
            },
            margin: o,
            ...d,
            ...n
        }),
        defaultValue: a
    })
}
const s$ = ({
        uploader: t,
        onSubmit: n,
        btnName: r
    }) => {
        const a = Lm(),
            i = a.formState.errors,
            {
                type: o
            } = qt(),
            {
                t: s
            } = Gn();
        return e.jsx(Ae, {
            maxWidth: "md",
            children: e.jsx(Ne, {
                children: e.jsxs(et, {
                    children: ["1" == o && t, e.jsxs("form", {
                        className: "mt-4 space-y-4",
                        onSubmit: n,
                        children: [e.jsx(oq, {
                            label: s("dashboard.unit"),
                            name: "unit_id",
                            control: a.control,
                            errors: i,
                            valueIsObject: !1,
                            options: []
                        }), e.jsx(oq, {
                            label: s("complaint.category"),
                            name: "category",
                            control: a.control,
                            errors: i,
                            valueIsObject: !1,
                            options: [{
                                id: 1,
                                name: "Service related complaint"
                            }, {
                                id: 1,
                                name: "Home related complaint"
                            }, {
                                id: 1,
                                name: "Property related complaint"
                            }, {
                                id: 1,
                                name: "Other"
                            }] || []
                        }), e.jsx(o$, {
                            label: s("complaint.description"),
                            name: "description",
                            errors: i,
                            control: a.control,
                            placeholder: "Please tell us more about the issue",
                            multiline: !0,
                            rows: 4
                        }), e.jsx(sP, {
                            justifyContent: "flex-end",
                            children: e.jsx(a$, {
                                fullWidth: !1,
                                type: "submit",
                                name: r,
                                children: s("common.save")
                            })
                        })]
                    })]
                })
            })
        })
    },
    l$ = "BUILDING_CU_BTN",
    d$ = aF,
    c$ = {
        category: "",
        description: "",
        unit_id: ""
    };

function u$({
    children: e,
    id: t,
    onImagesUploaded: n = () => {},
    onModelSaved: r = () => {}
}) {
    const a = bf({
            defaultValues: c$
        }),
        {
            images: i,
            setImages: o,
            handleDeleteFile: s,
            processUploading: l
        } = JZ();
    hq(a), t$(p$);
    const {
        model: d,
        handleSave: c
    } = gq({
        id: t,
        queryKey: [d$, {
            id: t
        }],
        queryFn: () => [],
        queryOptions: {
            onSuccess: e => {
                e.image && o([{
                    ...e.image,
                    state: QZ.COMPLETE
                }]), a.reset(e)
            }
        },
        btnName: l$,
        saveHandlerOptions: {
            formErrorSetter: a.setError,
            baseApiResourceUrl: "requests",
            requestConfig: ({
                isUpdate: e,
                modelId: t
            }) => ({
                method: "POST",
                url: "/issues",
                data: a.getValues()
            }),
            onAfterSuccess: e => {
                r({
                    request: e.data
                }), l({
                    id: t || e.data.id,
                    type: "request",
                    onSuccess: t => {
                        n({
                            request: e.data,
                            images: t
                        })
                    },
                    onSettled: () => {
                        fq(l$, !1)
                    }
                })
            }
        }
    });
    return e({
        images: i,
        setImages: o,
        model: d,
        id: t,
        form: a,
        handleDeleteFile: s,
        handleSave: a.handleSubmit(c)
    })
}
const p$ = Jq(null);

function h$() {
    const t = Ft(),
        {
            type: n
        } = qt();
    return e.jsx(u$, {
        id: n,
        onModelSaved: ({
            request: e
        }) => {
            t("/dashboard/issues")
        },
        onImagesUploaded: () => {},
        children: ({
            form: t,
            setImages: n,
            images: r,
            handleSave: a,
            handleDeleteFile: i
        }) => e.jsx(km, {
            ...t,
            children: e.jsx(s$, {
                onSubmit: a,
                btnName: l$,
                uploader: e.jsx(jq, {
                    onDeleteFile: i,
                    files: r,
                    onChange: e => n(e),
                    name: "buildingImage",
                    singleFile: !0
                })
            })
        })
    })
}
var m$ = (e => (e.INDIVIDUAL = "individual", e.COMPANY = "company", e))(m$ || {});
const f$ = e => ({
        id: e.id,
        first_name: e.first_name,
        last_name: e.last_name,
        national_id: e.national_id,
        nationality: e.nationality,
        phone_number: e.phone_number,
        phone_country_code: e.phone_country_code,
        email: e.email,
        gender: "string" == typeof e.gender ? e.gender : null,
        georgian_birthdate: e.georgian_birthdate ? tR(e.georgian_birthdate).format("YYYY-MM-DD") : null,
        invited: e.id ? 0 : e.invited ? 1 : 0,
        source: e.contactSource,
        documents: e.documents?.map(e => +e?.id)
    }),
    g$ = e => {
        const t = {
            id: e.id,
            name_en: e.name_en,
            name_ar: e.name_ar,
            first_name: e.primaryUser.first_name,
            last_name: e.primaryUser.last_name,
            phone_country_code: e.primaryUser.phone_country_code,
            phone_number: e.primaryUser.phone_number,
            commercial_registration_no: e.registrationNumber,
            tax_identifier_no: e.taxNumber,
            address: e.address,
            source: e.contactSource,
            website: e.website,
            email: e.primaryUser.email,
            gender: "string" == typeof e.primaryUser.gender ? e.primaryUser.gender : null,
            georgian_birthdate: e.primaryUser.georgian_birthdate ? tR(e.primaryUser.georgian_birthdate).format("YYYY-MM-DD") : null,
            national_id: e.primaryUser.national_id,
            nationality: e.primaryUser.nationality,
            related_companies: e?.related?.map(e => ({
                name_en: e.name_en,
                name_ar: e.name_ar,
                website: e.website,
                relation_type: e.relation,
                company_logo: +e.logo?.[0]?.id
            })),
            documents: e?.documents?.map(e => +e.id),
            invited: e.primaryUser.invited ? 1 : 0,
            contact_source: e.contactSource,
            company_logo: +e.logo?.[0]?.id
        };
        return t.company_logo || delete t.company_logo, 1 !== t.related_companies.length || t.related_companies[0].name_en || (t.related_companies = []), t.related_companies = t.related_companies.map(e => (e.company_logo || delete e.company_logo, e)), t
    },
    y$ = e => ({
        list: e?.data?.list?.map(e => ({
            id: e.id,
            number: e.contract_number,
            tenant: {
                name: e.tenant?.name,
                phone: e.tenant?.phone_number
            },
            unitName: e.units?.map(e => e.name).join(", "),
            buildingName: e.building?.name,
            communityName: e.community?.name,
            status: e.status?.id,
            statusName: e.status?.name,
            daysRemaining: e.days_remaining,
            lastModified: e.updated_at
        })),
        total: e?.data?.paginator?.total,
        pagesCount: e?.data?.paginator?.last_page
    }),
    v$ = async (e, t) => await uo(`/api-management/rf/${t}/${e?.id}`, e), _$ = async (e, t) => await co(`/api-management/rf/${t}`, e), x$ = async (e, t) => await uo(`/api-management/rf/${t}/${e?.id}`, e), b$ = async (e, t, n) => await co(`/api-management/rf/${e}/attach/property/${t}`, n), w$ = async (e, t) => await co(`/api-management/rf/${e}/change-status/${t}`), C$ = async (e, t) => await co(`/api-management/rf/companies/change-status/${t}`), M$ = async (e, t) => await lo(`/api-management/rf/${e}/${t}`), S$ = async (e, t, n) => await lo(`/api-management/rf/attach/building/${e}?is_paginate=1&query=${n}&page=${t}&is_active=1`), L$ = async (e, t, n) => await lo(`/api-management/rf/attach/community/${e}?is_paginate=1&query=${n}&page=${t}`), k$ = async e => await lo(`/api-management/rf/users/rates/${e}`), T$ = async (e, t) => await co(`/api-management/rf/professionals/attach/category/${t}`, e), j$ = async ({
        search: e,
        filter: t,
        page: n
    }) => await lo(`/api-management/contacts?role=${t}&search=${e}&sort_dir=latest&page=${n}&active=1`), E$ = async ({
        search: e,
        page: t
    }) => {
        try {
            const n = await lo(`/api-management/rf/tenants?query=${e}&sort_dir=latest&page=${t}&is_paginate=1&active=1&not_has_family_member=1`, {
                page: t,
                search: e
            });
            return O$(n)
        } catch (n) {
            throw n
        }
    }, D$ = async ({
        search: e,
        page: t
    }) => {
        try {
            const n = await lo(`/api-management/rf/companies?search=${e}&sort_dir=latest&page=${t}&is_paginate=1&is_active=1`, {
                page: t,
                search: e
            });
            return O$(n)
        } catch (n) {
            throw n
        }
    }, V$ = async ({
        search: e,
        page: t
    }) => {
        try {
            const n = await lo(`/api-management/rf/admins?query=${e}&sort_dir=latest&page=${t}&is_paginate=1&active=1`, {
                roles: [pU.Leasing, uU.Admin, uU.AccountAdmin],
                page: t,
                query: e
            });
            return O$(n)
        } catch (n) {
            throw n
        }
    }, A$ = async e => {
        try {
            return await lo(`/api-management/rf/companies/${e}`)
        } catch (t) {
            throw t
        }
    }, O$ = e => "list" in e?.data ? e?.data?.list?.map(e => ({
        id: e?.id,
        name: e?.name,
        name_ar: e?.name_ar,
        name_en: e?.name_en,
        phone_number: e?.phone_number,
        tax_identifier_no: e?.tax_identifier_no,
        commercial_registration_no: e?.commercial_registration_no,
        role: e?.role
    })) ?? [] : e?.data?.map(e => ({
        id: e?.id,
        name: e?.name,
        name_ar: e?.name_ar,
        name_en: e?.name_en,
        nationalId: e?.national_id,
        email: e?.email,
        phone_number: e?.phone_number,
        phone_country_code: e?.phone_country_code,
        tax_identifier_no: e?.tax_identifier_no,
        commercial_registration_no: e?.commercial_registration_no,
        role: e?.role
    })) ?? [], P$ = async (e, t) => await lo(`/api-management/rf/${t}/${e}`), I$ = async e => {
        try {
            const t = f$(e);
            await _$(t, "tenants")
        } catch (t) {
            throw t
        }
    }, F$ = async ({
        id: e
    }) => {
        try {
            const n = await lo(`/api-management/rf/related-companies?company_id=${e}`);
            return t = n, t?.data?.map(e => ({
                id: e.id,
                name_en: e.name_en,
                name_ar: e.name_ar,
                relation: e.relation_type,
                logo: e.company_logo,
                mode: "view",
                website: e?.website
            })) ?? []
        } catch (n) {
            throw n
        }
        var t
    }, H$ = async e => {
        try {
            const t = f$(e);
            await v$(t, "tenants")