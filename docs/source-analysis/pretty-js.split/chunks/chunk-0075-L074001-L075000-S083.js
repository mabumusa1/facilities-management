                x(!_)
            },
            ModalProps: {
                keepMounted: !0
            },
            sx: {
                display: {
                    xs: "block",
                    sm: "none"
                },
                "& .MuiDrawer-paper": {
                    boxSizing: "border-box",
                    width: LZ
                }
            },
            children: e.jsx(cW, {
                open: _,
                handleDrawerToggle: b
            })
        }), e.jsx(DZ, {
            variant: "permanent",
            open: m,
            sx: {
                display: {
                    xs: "none",
                    sm: "block"
                }
            },
            children: e.jsx(cW, {
                open: m,
                handleDrawerToggle: b
            })
        }), e.jsxs(cP, {
            layoutBackground: !0,
            component: "main",
            sx: {
                backgroundColor: "background.default",
                width: {
                    sm: "calc(100% - 260px)"
                },
                background: "#fafcfd",
                borderRadius: "4px",
                marginTop: {
                    xs: "12rem",
                    md: "11rem"
                },
                overflow: "auto",
                position: "relative",
                scrollbarGutter: "stable",
                marginBottom: {
                    xs: "55px",
                    md: "0"
                }
            },
            children: [e.jsx(sP, {
                sx: {
                    alignItems: "center",
                    mb: 4,
                    borderRadius: "4px"
                },
                children: e.jsx(FI, {
                    routes: T
                })
            }), e.jsx(ZI, {
                FallbackComponent: zI,
                onReset: () => {
                    d.clear()
                },
                onError: e => {},
                children: e.jsx(Dt.Suspense, {
                    fallback: e.jsx(hP, {}),
                    children: g ? e.jsx(CI, {}) : e.jsx(Zt, {})
                })
            }), e.jsx(cU, {
                notification: p
            }), u && e.jsx(UU, {})]
        })]
    })
}
const OZ = Dt.lazy(() => SZ(() => rr(() => import("./announcements-list.page-Cy0BOJOl.js"), __vite__mapDeps([0, 1, 2, 3, 4, 5, 6])))),
    PZ = Dt.lazy(() => SZ(() => rr(() => import("./announcements-form.page-vAjoAZtW.js"), __vite__mapDeps([7, 1, 2, 3, 8, 9, 10, 6, 11, 12, 13, 5])))),
    IZ = Dt.lazy(() => SZ(() => rr(() => import("./announcement-details.page-CCkB0gqg.js"), __vite__mapDeps([14, 1, 2, 3, 4, 6])))),
    FZ = [{
        title: "announcements",
        path: "announcements",
        children: [{
            title: "",
            path: "",
            element: e.jsx(OZ, {})
        }, {
            title: "create-announcements",
            path: "create",
            element: e.jsx(PZ, {}),
            nav: !0
        }, {
            title: "edit-announcements",
            path: "edit/:id",
            element: e.jsx(PZ, {}),
            nav: !0
        }, {
            title: "view-announcements",
            path: ":id",
            element: e.jsx(IZ, {}),
            nav: !0
        }]
    }];

function HZ({
    children: e
}) {
    return e
}
const NZ = Dt.lazy(() => SZ(() => rr(() => import("./ViewSuggestions-CRl31Bsf.js"), __vite__mapDeps([15, 1, 2, 3, 16, 6])))),
    RZ = Dt.lazy(() => SZ(() => rr(() => import("./overdues.page-BsguDD8c.js"), __vite__mapDeps([17, 1, 2, 3, 18, 19, 6])))),
    YZ = Dt.lazy(() => SZ(() => rr(() => import("./Suggestions-DbCLHW-W.js"), __vite__mapDeps([20, 1, 2, 3, 16, 6])))),
    BZ = [{
        title: "Overdues",
        path: "overdues",
        element: e.jsx(RZ, {}),
        nav: !0
    }, {
        title: "Suggestions",
        path: "suggestions",
        element: e.jsx(YZ, {}),
        nav: !0
    }, {
        title: "View-suggestions",
        path: "suggestions/:id",
        element: e.jsx(NZ, {}),
        nav: !0
    }],
    zZ = Dt.lazy(() => SZ(() => rr(() => import("./OfferForm-DfRfBUBS.js"), __vite__mapDeps([21, 1, 2, 3, 22, 13, 23, 6])))),
    UZ = Dt.lazy(() => SZ(() => rr(() => import("./OfferRequests-CO_LVS9k.js"), __vite__mapDeps([24, 1, 2, 3, 23, 25, 6])))),
    WZ = Dt.lazy(() => SZ(() => rr(() => import("./Offers-DNJ7GJk0.js"), __vite__mapDeps([26, 1, 2, 3, 23, 27, 6])))),
    ZZ = [{
        title: "Offers",
        path: "offers",
        element: e.jsx(WZ, {}),
        nav: !0
    }, {
        title: "Request-offer",
        path: "offers/:id/view",
        element: e.jsx(UZ, {}),
        nav: !0
    }, {
        title: "Create-offer",
        path: "offers/create",
        element: e.jsx(zZ, {}),
        nav: !0
    }],
    qZ = Dt.lazy(() => SZ(() => rr(() => Promise.resolve().then(() => S4), void 0))),
    $Z = Dt.lazy(() => SZ(() => rr(() => Promise.resolve().then(() => eae), void 0))),
    GZ = [{
        title: "Expired-Leases",
        path: "expiring-leases",
        element: e.jsx(qZ, {}),
        nav: !0
    }, {
        title: "View-expired-lease",
        path: "expiring-leases/:id",
        element: e.jsx($Z, {}),
        nav: !0
    }],
    KZ = "Something went wrong";
var QZ = (e => (e.UPLOADING = "UPLOADING", e.FAILED = "FAILED", e.COMPLETE = "COMPLETE", e.PENDING = "PENDING", e.DELETING = "DELETING", e))(QZ || {});

function JZ() {
    const [e, t] = Dt.useState([]);
    return {
        images: e,
        setImages: t,
        processUploading: async ({
            id: n,
            type: r,
            cImages: a = [],
            onSuccess: i,
            onError: o,
            onSettled: s
        }) => {
            e.length > 0 && (a = [...e]);
            const l = new FormData;
            l.append("model_id", n), l.append("model_type", r), a.forEach(e => {
                l.append("image[]", e)
            });
            try {
                await bo.post("/images/multiple", l)
            } catch (d) {
                (e => {
                    if (!So(e) || 422 !== e?.response?.status) return KZ;
                    const t = e?.response?.data?.errors?.image;
                    t && t[0]
                })(d)
            }
            s && s(a), a.every(e => e.state !== QZ.FAILED) ? i && i(a) : o && o(a), t(a)
        },
        handleDeleteFile: n => bo.delete(`/images/${n}`).then(() => {
            const r = e.findIndex(e => e.id === n);
            if (-1 === r) return;
            const a = [...e];
            a.splice(r, 1), t(a)
        }).catch(() => {
            const r = [...e],
                a = r.find(e => e.id === n);
            a && (a.error = KZ, t(r))
        })
    }
}

function XZ(e = {}) {
    return tl(["CITIES_ALL"], async () => await (async () => (await lo("/tenancy/api/cities/all")).data)())
}
const eq = Dt.forwardRef(({
        label: t,
        errors: n,
        className: r,
        name: a,
        disabled: i,
        margin: o = "normal",
        ...s
    }, l) => {
        const {
            t: d,
            i18n: c
        } = Gn(), [u, p] = Dt.useState("");
        let h;
        Dt.useMemo(() => u && 0 !== u.length ? s.options?.filter(e => ((e, t) => e?.toLowerCase()?.indexOf(t?.toLowerCase()) > -1)(e.name, u)) : s?.options, [u, s?.options]);
        let m = n && a ? bh.get(n, a) : null;
        return m && (m.id?.type && (m = m.id), h = "required" === m.type ? "en" === c.language ? "This is required" : "هذا مطلوب" : m.message ? m.message : "en" === c.language ? "This is invalid" : "هذا غير صالح"), delete s?.valueIsObject, s.options && s.options?.length > 0 ? e.jsx("div", {
            className: r,
            children: e.jsx(O, {
                name: a,
                disablePortal: !0,
                id: "combo-box-demo",
                options: s.options,
                autoHighlight: !0,
                getOptionLabel: e => e.name,
                renderOption: (t, n) => e.jsxs(cP, {
                    component: "li",
                    ...t,
                    children: [n.name_ar && "ar" === c.language ? n.name_ar : n.name, "a"]
                }),
                ...s,
                renderInput: n => e.jsx(E, {
                    ...n,
                    ...s,
                    label: t,
                    inputProps: {
                        ...n.inputProps
                    },
                    name: a,
                    error: !!h,
                    id: a,
                    disabled: i
                })
            })
        }) : e.jsx(e.Fragment, {})
    }),
    tq = {
        autoFocus: !1,
        PaperProps: {
            style: {
                maxHeight: 416,
                width: 300
            }
        }
    },
    nq = Dt.forwardRef(({
        label: t,
        errors: n,
        className: r,
        name: a,
        disabled: i,
        margin: o = "normal",
        isPhone: s = !1,
        placeholder: l,
        ...d
    }, c) => {
        const {
            t: u,
            i18n: p
        } = Gn(), [h, m] = Dt.useState(!1), [f, g] = Dt.useState([]), y = "rtl" === p.dir(), [v, _] = Dt.useTransition(), [x, b] = Dt.useState("");
        Dt.useEffect(() => {
            g(d?.options)
        }, [d?.options]);
        const w = e => {
                b(e);
                const t = e && 0 !== e?.length ? d.options?.filter(t => {
                    return n = s ? t.listName : t.name, r = e, n?.toLowerCase()?.indexOf(r?.toLowerCase()) > -1 || "string" == typeof t?.id && t?.id?.toLowerCase() === e.toLowerCase();
                    var n, r
                }) : d?.options;
                _(() => g(t))
            },
            {
                errorMsg: C
            } = (() => {
                let e, t = n && a ? bh.get(n, a) : null;
                return t && (t.id?.type && (t = t.id), e = t.message ? t.message : "required" === t.type ? "en" === p.language ? "This is required" : "هذا مطلوب" : "en" === p.language ? "This is invalid" : "هذا غير صالح"), {
                    errorMsg: e,
                    error: t
                }
            })(),
            M = d?.valueIsObject,
            S = e => d?.options?.find(t => t.id === e) ?? {
                id: "SA",
                name: "(+966)"
            },
            L = {
                name: a,
                error: !!C,
                labelId: t,
                ref: c,
                IconComponent: () => e.jsx(rq, {
                    isOpen: h,
                    setIsOpen: m,
                    disabled: i
                }),
                onOpen: () => m(!0),
                onClose: () => m(!1),
                label: t,
                disabled: i,
                id: a,
                sx: {
                    cursor: "pointer",
                    direction: "ltr"
                },
                open: h
            };
        return e.jsx(cP, {
            className: r,
            sx: {
                "& #phone_country_code": {
                    p: 0,
                    display: "flex",
                    justifyContent: "space-around",
                    position: "relative",
                    "&:after": {
                        content: '""',
                        position: "absolute",
                        top: "15%",
                        right: 0,
                        background: e => e.palette.divider,
                        width: "2px",
                        height: "70%"
                    }
                }
            },
            children: e.jsxs(k, {
                fullWidth: !0,
                size: "medium",
                margin: o,
                children: [e.jsx(P, {
                    id: t,
                    color: C ? "error" : "primary",
                    children: t
                }), M ? e.jsxs(I, {
                    ...L,
                    ...d,
                    sx: {
                        ...L.sx,
                        "& [aria-expanded=false]": {
                            color: d.value?.length ? "black" : "gray",
                            fontWeight: 300
                        },
                        "& [aria-expanded=true]": {
                            color: d.value?.length ? "black" : "gray",
                            fontWeight: 300
                        },
                        "& .MuiSelect-select": {
                            border: "none !important"
                        }
                    },
                    MenuProps: tq,
                    value: JSON.stringify(d.value),
                    onChange: e => {
                        e.target.value = JSON.parse(e.target.value), d.onChange(e)
                    },
                    renderValue: t => l && !JSON.parse(t)?.name ? e.jsx(rP, {
                        sx: {
                            color: e => e.palette.text.disabled,
                            textTransform: "Capitalize"
                        },
                        light: !0,
                        variant: "body",
                        children: l
                    }) : s ? e.jsx(aq, {
                        value: JSON.parse(t)?.name,
                        img: `https://flagcdn.com/${JSON.parse(t)?.id?.toLowerCase()}.svg`
                    }) : "null" !== t ? e.jsx(rP, {
                        color: "black",
                        s: 18,
                        sx: {
                            fontWeight: "300"
                        },
                        children: JSON.parse(t)?.name
                    }) : JSON.parse(t)?.name,
                    children: [e.jsx(F, {
                        sx: {
                            mb: 4
                        },
                        children: e.jsx(iq, {
                            search: x,
                            searchFn: w
                        })
                    }), f?.map(t => e.jsx(H, {
                        value: JSON.stringify(t),
                        sx: {},
                        disabled: t.disabled,
                        children: s ? e.jsx(aq, {
                            value: t?.listName,
                            img: `https://flagcdn.com/${t?.id?.toLowerCase()}.svg`
                        }) : e.jsxs(cP, {
                            column: !0,
                            children: [e.jsx(rP, {
                                light: !0,
                                variant: "body",
                                children: t?.name
                            }), e.jsx(rP, {
                                s: 13,
                                light: !0,
                                dir: y ? "rtl" : "ltr",
                                variant: "body",
                                children: t?.subtitle
                            })]
                        })
                    }, t?.id))]
                }) : e.jsxs(I, {
                    value: d.value,
                    ...d,
                    style: {
                        backgroundColor: i ? "#F0F0F0" : "white"
                    },
                    MenuProps: tq,
                    renderValue: t => s ? e.jsx(aq, {
                        value: S(t)?.name,
                        img: `https://flagcdn.com/${S(t)?.id?.toLowerCase()}.svg`
                    }) : t || t?.length ? e.jsx(rP, {
                        color: "black",
                        s: 18,
                        sx: {
                            fontWeight: "300"
                        },
                        variant: "body",
                        children: S(t)?.name
                    }) : l,
                    displayEmpty: !0,
                    ...L,
                    sx: {
                        ...L.sx,
                        "& [aria-expanded=false]": {
                            color: d.value?.length ? "black" : "#a9a9a9",
                            fontWeight: 300
                        },
                        "& [aria-expanded=true]": {
                            color: d.value?.length ? "black" : "#a9a9a9",
                            fontWeight: 300
                        }
                    },
                    children: [e.jsx(F, {
                        sx: {
                            mb: 4
                        },
                        children: e.jsx(iq, {
                            search: x,
                            searchFn: w
                        })
                    }), f?.map(t => e.jsx(H, {
                        value: t.id,
                        sx: {
                            py: 2
                        },
                        disabled: t.disabled,
                        children: s ? e.jsx(aq, {
                            value: t?.listName,
                            img: `https://flagcdn.com/${t?.id?.toLowerCase()}.svg`
                        }) : e.jsxs(cP, {
                            column: !0,
                            children: [e.jsx(rP, {
                                variant: "h6",
                                light: !0,
                                children: t?.name
                            }), !!t?.subtitle && e.jsx(rP, {
                                s: 12,
                                light: !0,
                                variant: "body",
                                children: t?.subtitle
                            })]
                        })
                    }, t.id))]
                }), C ? e.jsx(cP, {
                    sx: {
                        ml: -4
                    },
                    children: e.jsx(N, {
                        style: {
                            color: "#d32f2f"
                        },
                        children: C
                    })
                }) : []]
            })
        })
    }),
    rq = ({
        isOpen: t,
        setIsOpen: n,
        disabled: r
    }) => {
        const a = () => {
            r || n(!t)
        };
        return t ? e.jsx(Zf, {
            sx: {
                mr: "2px"
            },
            onClick: a
        }) : e.jsx(Bf, {
            sx: {
                mr: "2px"
            },
            onClick: a
        })
    },
    aq = ({
        value: t,
        img: n
    }) => e.jsxs(cP, {
        sx: {
            display: "flex",
            alignItems: "center",
            gap: "0.5rem"
        },
        children: [e.jsx(cP, {
            component: "img",
            src: n,
            sx: {
                maxWidth: "100%",
                maxHeight: "100%",
                width: "3rem",
                height: "2rem"
            }
        }), e.jsx(rP, {
            light: !0,
            s: 16,
            color: "black",
            children: t
        })]
    }),
    iq = ({
        searchFn: t,
        search: n
    }) => {
        const {
            t: r
        } = Gn();
        return e.jsx(E, {
            size: "small",
            placeholder: r("common.search"),
            fullWidth: !0,
            value: n,
            InputProps: {
                startAdornment: e.jsx(j, {
                    position: "start",
                    children: e.jsx(yh, {})
                })
            },
            onChange: e => t(e.target.value),
            onKeyDown: e => {
                "Escape" !== e.key && e.stopPropagation()
            }
        })
    };

function oq({
    name: t,
    control: n,
    rules: r,
    disabled: a = !1,
    defaultValue: i = "",
    newComp: o,
    isPhone: s = !1,
    label: l,
    onChange: d = () => {},
    labelSize: c = 14,
    isDark: u = !1,
    ...p
}) {
    const {
        i18n: h
    } = Gn(), m = "rtl" === h.dir();
    return e.jsx(Mm, {
        control: n,
        name: t,
        rules: r,
        defaultValue: i,
        render: ({
            field: t
        }) => e.jsx(e.Fragment, {
            children: e.jsxs(cP, {
                column: !0,
                children: [e.jsx(rP, {
                    variant: "caption",
                    s: c,
                    sx: {
                        mb: "-10px",
                        fontWeight: 400,
                        ...u ? {
                            color: "#fff"
                        } : {},
                        textWrap: "nowrap"
                    },
                    children: l
                }), e.jsx(cP, {
                    sx: {
                        direction: m && s ? "rtl" : "ltr"
                    },
                    children: o ? e.jsx(eq, {
                        disabled: a,
                        onClick: p?.onClick,
                        ...p,
                        ...t
                    }) : e.jsx(nq, {
                        disabled: a,
                        ...p,
                        ...t,
                        onChange: e => {
                            t?.onChange(e), d(e)
                        },
                        isPhone: s,
                        value: p?.options?.find(e => e?.id === t?.value?.id) || t?.value
                    })
                })]
            })
        })
    })
}

function sq({
    control: t,
    name: n,
    onChange: r,
    ...a
}) {
    const i = XZ(),
        {
            t: o
        } = Gn();
    return e.jsx(oq, {
        options: i?.data,
        control: t,
        placeholder: o("selectCity"),
        name: n,
        onChange: r,
        ...a
    })
}

function lq(e = {}) {
    const {
        cityId: t,
        ...n
    } = e;
    return tl(["DISTRICTS_ALL", {
        cityId: t
    }], async () => await (async e => (await lo("/tenancy/api/districts/all", {
        city_id: e
    })).data)(t))
}

function dq({
    control: t,
    name: n,
    cityFieldName: r,
    onChange: a,
    ...i
}) {
    const o = wm({
            control: t,
            name: r,
            defaultValue: null
        }),
        {
            t: s
        } = Gn(),
        l = lq({
            cityId: o?.id
        });
    return e.jsx(oq, {
        placeholder: s("selectDistrict"),
        onChange: a,
        options: l.data || [],
        control: t,
        name: n,
        ...i
    })
}
const cq = {
        belongTo: "belong_to",
        city: "city",
        district: "district"
    },
    uq = {
        hideBelongToField: !1
    };

function pq({
    form: t,
    names: n,
    options: r,
    rules: a,
    onChange: i,
    margin: o = "normal",
    columnWidth: s,
    sx: l
}) {
    const d = t.formState.errors,
        {
            t: c
        } = Gn(),
        u = Dt.useMemo(() => ({
            ...cq,
            ...n
        }), [n]),
        {
            hideBelongToField: p
        } = Dt.useMemo(() => ({
            ...uq,
            ...r
        }), [r]);
    return e.jsxs(e.Fragment, {
        children: [e.jsx(lP, {
            xs: s,
            sx: l,
            children: e.jsx(sq, {
                margin: o,
                label: `${c("newPropertyForm.city")}${a?.city?.required?"*":""}`,
                rules: a?.city,
                control: t.control,
                errors: d,
                name: u.city,
                valueIsObject: !0,
                onChange: e => {
                    t.setValue(u.district, null), t.trigger(u.city), i?.(e)
                }
            })
        }), e.jsx(lP, {
            xs: s,
            sx: l,
            children: e.jsx(dq, {
                margin: o,
                cityFieldName: u.city,
                control: t.control,
                errors: d,
                name: u.district,
                rules: a?.city,
                label: `${c("newPropertyForm.district")}${a?.city?.required?"*":""}`,
                valueIsObject: !0,
                disabled: !t.getValues(u.city),
                onChange: i
            })
        })]
    })
}

function hq(e, t) {
    const n = Dt.useMemo(() => ({
            ...cq,
            ...t
        }), [t]),
        r = wm({
            control: e.control,
            defaultValue: null,
            name: n.city
        }),
        a = e.formState.isDirty;
    Dt.useEffect(() => {
        a && e.setValue(n.district, null)
    }, [r])
}
const mq = new Map;

function fq(e, t) {
    const n = mq.get(e);
    n && n(t)
}

function gq({
    id: e,
    queryKey: t,
    queryFn: n,
    queryOptions: r,
    btnName: a,
    saveHandlerOptions: i = {}
}) {
    const o = !!e,
        s = Ys();
    return {
        model: tl(t, n, {
            enabled: o,
            refetchOnMount: "always",
            ...r
        }),
        isUpdate: o,
        handleSave: n => {
            const r = i.requestConfig ? i.requestConfig({
                isUpdate: o,
                modelId: e
            }) : {};
            fq(a, !0);
            const l = n,
                d = l?.file;
            Array.isArray(r) ? r.map(async (l, c) => await bo.request({
                url: o ? `/${i.baseApiResourceUrl}/${e}` : `/${i.baseApiResourceUrl}`,
                method: o ? "PUT" : "POST",
                data: n,
                ...l
            }).then(({
                data: n
            }) => {
                if (s.invalidateQueries(t?.[0]), i.onSuccess && c === l.Config.length - 1) return void i.onSuccess(n);
                let a = e;
                e || (a = n?.data?.id?.toString()), i.onAfterSuccess && c === r.length - 1 && i.onAfterSuccess(d ? {
                    ...n,
                    file: d
                } : n)
            }).catch(e => {
                i.onError ? i.onError(e) : (fq(a, !1), So(e) ? Lo(e, {
                    setError: i.formErrorSetter
                }) : Zi.error(KZ))
            })) : bo.request({
                url: o ? `/${i.baseApiResourceUrl}/${e}` : `/${i.baseApiResourceUrl}`,
                method: o ? "PUT" : "POST",
                data: n,
                ...r
            }).then(({
                data: n
            }) => {
                if (s.invalidateQueries(t[0]), i.onSuccess) return void i.onSuccess(n);
                let r = e;
                e || (r = n?.data?.id?.toString()), i.onAfterSuccess && i.onAfterSuccess(d ? {
                    ...n,
                    file: d
                } : n)
            }).catch(e => {
                const t = e?.response?.data?.errors;
                t && Object.keys(t)?.length && Object.values(t).forEach(e => {
                    Zi.error(e[0], {
                        toastId: e[0]
                    })
                }), i.onError ? i.onError(e) : (fq(a, !1), So(e) ? Lo(e, {
                    setError: i.formErrorSetter
                }) : Zi.error(KZ))
            })
        }
    }
}
var yq, vq = {};

function _q() {
    if (yq) return vq;
    yq = 1;
    var e = h();
    Object.defineProperty(vq, "__esModule", {
        value: !0
    }), vq.default = void 0;
    var t = e(jp()),
        n = m();
    return vq.default = (0, t.default)((0, n.jsx)("path", {
        d: "M21 19V5c0-1.1-.9-2-2-2H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2M8.9 13.98l2.1 2.53 3.1-3.99c.2-.26.6-.26.8.01l3.51 4.68c.25.33.01.8-.4.8H6.02c-.42 0-.65-.48-.39-.81L8.12 14c.19-.26.57-.27.78-.02"
    }), "ImageRounded"), vq
}
const xq = It(_q());
var bq, wq = {};

function Cq() {
    if (bq) return wq;
    bq = 1;
    var e = h();
    Object.defineProperty(wq, "__esModule", {
        value: !0
    }), wq.default = void 0;
    var t = e(jp()),
        n = m();
    return wq.default = (0, t.default)((0, n.jsx)("path", {
        d: "M18.3 5.71a.9959.9959 0 0 0-1.41 0L12 10.59 7.11 5.7a.9959.9959 0 0 0-1.41 0c-.39.39-.39 1.02 0 1.41L10.59 12 5.7 16.89c-.39.39-.39 1.02 0 1.41.39.39 1.02.39 1.41 0L12 13.41l4.89 4.89c.39.39 1.02.39 1.41 0 .39-.39.39-1.02 0-1.41L13.41 12l4.89-4.89c.38-.38.38-1.02 0-1.4"
    }), "ClearRounded"), wq
}
const Mq = It(Cq());

function Sq({
    files: t,
    onDeleteFile: n,
    singleFile: r
}) {
    return e.jsx(ge, {
        children: t.map(t => e.jsxs(ye, {
            children: [e.jsx(Je, {
                children: e.jsx(f, {
                    src: t.url ? t.url : void 0,
                    children: e.jsx(xq, {})
                })
            }), e.jsx(Ue, {
                primary: t.file ? t.file.name : "",
                secondary: t.error ? t.error : null,
                classes: {
                    secondary: "error"
                }
            }), e.jsx(Xe, {
                children: e.jsx(w, {
                    edge: "end",
                    "aria-label": "delete",
                    onClick: () => n(t.id),
                    size: "large",
                    children: e.jsx(Mq, {})
                })
            })]
        }, t.id))
    })
}
const Lq = Dt.forwardRef(({
        name: t,
        onChange: n,
        options: r = {}
    }, a) => {
        const {
            maxImagesLength: i = 5,
            maxFilesLength: s = 1
        } = r;
        return e.jsx(sP, {
            sx: kq.mainContainer,
            children: e.jsxs("div", {
                className: "space-y-1 text-center",
                children: [e.jsx("svg", {
                    className: "w-12 h-12 mx-auto text-gray-400",
                    stroke: "currentColor",
                    fill: "none",
                    viewBox: "0 0 48 48",
                    "aria-hidden": "true",
                    children: e.jsx("path", {
                        d: "M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02",
                        strokeWidth: 2,
                        strokeLinecap: "round",
                        strokeLinejoin: "round"
                    })
                }), e.jsx("div", {
                    className: "flex pt-4",
                    children: e.jsxs("label", {
                        htmlFor: t,
                        className: "h-full left-0 top-3 pt-16 w-full absolute block font-medium text-center rounded-md cursor-pointer",
                        children: [e.jsx(o, {
                            variant: "subtitle2",
                            className: "text-center",
                            children: "Upload an images and files"
                        }), e.jsx("input", {
                            type: "file",
                            id: t,
                            name: t,
                            ref: a,
                            onChange: n,
                            className: "hidden"
                        })]
                    })
                }), i ? e.jsxs(o, {
                    className: "py-2",
                    variant: "body2",
                    children: ["PNG, JPG, GIF up to ", i]
                }) : [], s ? e.jsxs(o, {
                    variant: "body2",
                    children: ["Max ", s, " file"]
                }) : []]
            })
        })
    }),
    kq = {
        mainContainer: {
            border: 2,
            borderColor: "#555",
            borderStyle: "dashed",
            borderRadius: "0.375rem"
        }
    };

function Tq(e) {
    return e = e.file ? e.file : e, e?.file ? "image" === e?.file?.type?.split("/")?.[0] : "image" === e?.type?.split("/")?.[0]
}

function jq({
    files: t = [],
    onChange: n,
    singleFile: r = !1,
    onDeleteFile: i,
    name: o,
    options: s = {}
}) {
    return e.jsxs(a, {
        sx: {
            p: 4
        },
        children: [e.jsx(Lq, {
            onChange: e => {
                if (!e.target.files?.length) return;