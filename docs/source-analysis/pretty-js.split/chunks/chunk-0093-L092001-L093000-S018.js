            }) : e.jsx(cP, {
                sx: {
                    display: "grid",
                    gap: "41px",
                    gridTemplateColumns: "repeat(3, 1fr)"
                },
                "data-testid": "facilities-list",
                children: d?.map(t => e.jsx($6, {
                    facility: t
                }, t.id))
            })]
        })
    },
    Q6 = Object.freeze(Object.defineProperty({
        __proto__: null,
        default: K6
    }, Symbol.toStringTag, {
        value: "Module"
    })),
    J6 = ({
        image: t,
        modelType: n,
        modelID: r,
        setImageUrl: i,
        deleteImage: o,
        onClick: s,
        sx: d = {}
    }) => {
        const [c, u] = Dt.useState(!1), {
            t: p
        } = Gn();
        return e.jsxs(a, {
            height: 320,
            onMouseEnter: () => u(!0),
            onMouseLeave: () => u(!1),
            sx: {
                position: "relative",
                cursor: "pointer",
                ...d
            },
            onClick: s,
            children: [e.jsx(a, {
                component: "img",
                "data-testid": "image-preview",
                src: t.url,
                alt: t.name,
                sx: {
                    boxShadow: "rgba(0, 0, 0, 0.05) 0px 6px 24px 0px, rgba(0, 0, 0, 0.08) 0px 0px 0px 1px",
                    borderRadius: "8px",
                    objectFit: "cover",
                    width: "100%",
                    height: "100%"
                }
            }, t.id), c && r && e.jsx(l, {
                variant: "contained",
                color: "error",
                onClick: e => (async e => {
                    if (o) o(e, i);
                    else try {
                        await bo.delete(`/images/${e}`), await bo.get(`/images/models?model_type=${n}&model_id=${r}`).then(e => {
                            let t = e?.data?.data;
                            i(t)
                        }), Zi.success(p("common.filesDeletedSuccessfully"))
                    } catch (t) {
                        Lo(t)
                    }
                })(t.id),
                sx: {
                    px: 2,
                    mx: 3,
                    minWidth: "30px",
                    position: "absolute",
                    top: 10,
                    right: 10,
                    cursor: "pointer"
                },
                children: e.jsx(th, {})
            })]
        })
    },
    X6 = ({
        images: t,
        modelType: n,
        modelID: r,
        isDeleteShown: i,
        isDeleting: o,
        deleteImage: s,
        isShowAll: d,
        maxShown: c = 3,
        gridProps: u,
        onClick: p = e => window.open(t[e].url, "__blank")
    }) => {
        const [h, m] = Dt.useState([]);
        return Dt.useEffect(() => {
            t?.length ? (c && m(t?.slice(0, c)), m(t)) : m([])
        }, [t]), e.jsx(e.Fragment, {
            children: h && !!h.length && e.jsx(a, {
                sx: {
                    display: "grid",
                    alignItems: "center",
                    gridTemplateColumns: `repeat(${Math.min(h.length,c)}, 1fr)`,
                    gap: 2,
                    mb: 6,
                    ...u
                },
                children: h.slice(0, d ? void 0 : c).map((t, d) => e.jsxs(a, {
                    sx: {
                        position: "relative"
                    },
                    className: "image-preview",
                    children: [i && e.jsx(l, {
                        disabled: o,
                        sx: {
                            position: "absolute",
                            zIndex: "2",
                            right: 0
                        },
                        onClick: () => s(d, t?.id),
                        children: e.jsx(th, {
                            sx: {
                                color: o ? "GrayText" : "red"
                            }
                        })
                    }), e.jsx(J6, {
                        image: t,
                        modelType: n,
                        modelID: r,
                        setImageUrl: m,
                        deleteImage: s,
                        onClick: () => {
                            p(d), window.open(t.url, "__blank")
                        }
                    })]
                }))
            })
        })
    },
    e5 = "DELETE_PROFILE";

function t5({
    queryKey: t,
    title: n,
    body: r,
    handleClose: a,
    isOpen: i,
    deleteFunc: o,
    to: s,
    onSuccess: l,
    hidetoast: d = !1
}) {
    const {
        t: c
    } = Gn(), u = Ft(), p = Ys();
    return e.jsxs(v, {
        onClose: a,
        open: i,
        fullWidth: !0,
        maxWidth: "sm",
        children: [e.jsx(TJ, {
            title: c(""),
            handleClose: a
        }), e.jsxs(_, {
            sx: {
                my: "58px"
            },
            children: [e.jsx(cP, {
                sx: {
                    textAlign: "center",
                    justifyContent: "center",
                    display: "flex"
                },
                children: e.jsx(cP, {
                    sx: {
                        width: "67px",
                        height: "67px"
                    },
                    component: "img",
                    src: KW,
                    alt: "close"
                })
            }), e.jsxs(cP, {
                ycenter: !0,
                column: !0,
                sx: {},
                children: [e.jsx(rP, {
                    align: "center",
                    s: 24,
                    sx: {
                        mt: "22px"
                    },
                    children: n
                }), e.jsx(rP, {
                    variant: "subtitle1",
                    sx: {
                        mt: "8px",
                        fontWeight: 400,
                        textAlign: "center",
                        maxWidth: "360px"
                    },
                    children: r
                })]
            }), e.jsxs(cP, {
                sx: {
                    display: "flex",
                    justifyContent: "center",
                    alignItems: "center",
                    flexDirection: "column",
                    mt: "60px"
                },
                children: [e.jsx(a$, {
                    name: e5,
                    sx: {
                        px: "130px",
                        marginBottom: "20px",
                        py: "10px"
                    },
                    color: "error",
                    variant: "contained",
                    onClick: async () => {
                        fq(e5, !0);
                        try {
                            await o(), d || Zi.success(c("common.success")), t && await p.invalidateQueries(Array.isArray(t) ? t : [t]), l && l(), s && u(s), a()
                        } catch (e) {
                            fq(e5, !1), Lo(e, {}, !0)
                        }
                        fq(e5, !1)
                    },
                    fullWidth: !1,
                    children: c("ApproveVisitorForm.yes")
                }), e.jsx(dP, {
                    sx: {
                        px: "130px",
                        marginBottom: "10px",
                        color: "inherit"
                    },
                    onClick: a,
                    children: c("common.no")
                })]
            })]
        })]
    })
}
var n5, r5 = {};

function a5() {
    if (n5) return r5;
    n5 = 1;
    var e = h();
    Object.defineProperty(r5, "__esModule", {
        value: !0
    }), r5.default = void 0;
    var t = e(jp()),
        n = m();
    return r5.default = (0, t.default)((0, n.jsx)("path", {
        d: "M3 21h3.75L17.81 9.94l-3.75-3.75L3 17.25zm2-2.92 9.06-9.06.92.92L5.92 19H5zM18.37 3.29a.9959.9959 0 0 0-1.41 0l-1.83 1.83 3.75 3.75 1.83-1.83c.39-.39.39-1.02 0-1.41z"
    }), "ModeEditOutlined"), r5
}
const i5 = It(a5());

function o5() {
    const {
        t: t
    } = Gn(), {
        facilityID: n
    } = qt(), [r] = $t(), a = Ft(), [i, o] = Dt.useState(!1);
    return e.jsxs(e.Fragment, {
        children: [e.jsxs(cP, {
            component: "header",
            mb: "28px",
            children: [e.jsx(IQ, {
                color: "inherit",
                sx: {
                    display: "flex",
                    alignItems: "center",
                    gap: 2,
                    p: "10px 20px",
                    borderRadius: "8px",
                    border: "1px solid #CACACA",
                    mb: "28px"
                },
                handleBackAction: () => {
                    const e = r.get("is_settings");
                    e && !["undefined", 0].includes(e) ? a(`/settings/facilities?complex_id=${r.get("complex_id")}&community_name=${r.get("community_name")}&is_settings=1`) : a(`/settings/facilities?complex_id=${r.get("complex_id")}`)
                }
            }), e.jsxs(cP, {
                sx: {
                    display: "flex",
                    alignItems: "center",
                    justifyContent: "space-between"
                },
                children: [e.jsx(rP, {
                    variant: "subtitle1",
                    s: 36,
                    children: t("facilityDetails")
                }), e.jsxs(cP, {
                    row: !0,
                    gap: "32px",
                    children: [e.jsx(dP, {
                        onClick: () => o(!0),
                        color: "error",
                        sx: {
                            p: "12px 24px"
                        },
                        variant: "outlined",
                        children: t("deleteFacility")
                    }), e.jsx(dP, {
                        onClick: () => {
                            a(`/settings/addNewFacility/${n}?complex_id=${r.get("complex_id")}`)
                        },
                        startIcon: e.jsx(i5, {}),
                        sx: {
                            p: "12px 24px"
                        },
                        color: "primary",
                        variant: "outlined",
                        children: t("edit")
                    })]
                })]
            })]
        }), e.jsx(t5, {
            isOpen: i,
            deleteFunc: async () => {
                try {
                    await (async e => await po(`/api-management/rf/facilities/${e}`))(n), a(`/settings/facilities?complex_id=${r.get("complex_id")}`)
                } catch (e) {
                    Lo(e)
                }
            },
            queryKey: [AF],
            title: t("deleteFacility"),
            body: t("areYouSureToDeleteFacility"),
            handleClose: () => o(!1)
        })]
    })
}
const s5 = () => {
        const {
            t: t,
            i18n: {
                language: n
            }
        } = Gn(), {
            facilityID: r
        } = qt(), {
            data: a,
            isLoading: i
        } = tl([eH, r], async () => await D6(r), {
            cacheTime: 0,
            enabled: !!r
        }), o = P6(t).facilityTypes, s = [{
            label: "toolBooking.view.CommunityName",
            value: a?.community?.name
        }, {
            label: "facilitiesBooking.facilityType",
            value: R6(o, a?.booking_type)
        }, {
            label: "bookingDuration",
            value: a?.reservation_duration ? z6(a?.reservation_duration, t, n) : t("noLimits")
        }, {
            label: "facilitiesBooking.limits_placeholder",
            value: a?.capacity ?? t("noLimits")
        }, {
            label: "workingDays",
            value: "ar" === n ? W6({
                days: a?.days,
                t: t
            }) : U6({
                days: a?.days,
                t: t
            })
        }, {
            label: "facilitiesBooking.workingTime",
            value: Z6({
                data: a,
                timeFormatter: e => q6(e, t),
                t: t
            })
        }, {
            label: "ageLimit",
            value: Number(a?.age) ? a?.age : t("noLimits")
        }, {
            label: "gender",
            value: t(`${a?.gender}`)
        }].filter(e => a?.booking_type === O6.SHARED ? "bookingDuration" !== e.label && "facilitiesBooking.limits_placeholder" !== e.label : e);
        return i ? e.jsx(hP, {}) : e.jsxs(cP, {
            maxWidth: "lg",
            mx: "auto",
            pb: 20,
            children: [e.jsx(o5, {}), a?.images && 0 !== a?.images?.length && e.jsx(X6, {
                images: [{
                    url: a?.images?.url,
                    id: a?.images?.id,
                    name: a?.images?.name
                }]
            }), e.jsxs(Ne, {
                sx: {
                    p: "16px 24px",
                    my: "24px"
                },
                children: [e.jsxs(cP, {
                    component: "section",
                    mb: "24px",
                    children: [e.jsx(rP, {
                        sx: {
                            mb: "8px",
                            textTransform: "capitalize"
                        },
                        s: 16,
                        light: !0,
                        children: t("facilitiesBooking.facilityName")
                    }), e.jsx(rP, {
                        sx: {
                            textTransform: "capitalize"
                        },
                        s: 18,
                        children: "ar" === n ? a?.name_ar : a?.name_en
                    })]
                }), a?.description && e.jsxs(cP, {
                    component: "section",
                    children: [e.jsx(rP, {
                        mb: "8px",
                        s: 24,
                        children: t("serviceProvider.Description")
                    }), e.jsx(rP, {
                        s: 16,
                        sx: {
                            lineHeight: "17.68px",
                            mb: "8px"
                        },
                        light: !0,
                        children: a?.description
                    })]
                })]
            }), e.jsxs(Ne, {
                sx: {
                    p: "16px 24px"
                },
                children: [e.jsx(rP, {
                    s: 18,
                    mb: "32px",
                    children: t("facilitiesBooking.additional_details")
                }), e.jsx(cP, {
                    component: "section",
                    sx: {
                        display: "grid",
                        gridTemplateColumns: "repeat(4, 1fr)",
                        gap: "24px 4px"
                    },
                    children: s.map(({
                        label: n,
                        value: r
                    }) => e.jsxs(cP, {
                        children: [e.jsx(rP, {
                            s: 12,
                            light: !0,
                            children: t(n)
                        }), e.jsx(rP, {
                            "data-testid": n,
                            s: 16,
                            lineBreak: "auto",
                            children: r
                        })]
                    }, n))
                })]
            })]
        })
    },
    l5 = Object.freeze(Object.defineProperty({
        __proto__: null,
        default: s5
    }, Symbol.toStringTag, {
        value: "Module"
    }));

function d5({
    name: t,
    label: n,
    labels: r,
    control: a,
    errors: i,
    onFocus: s,
    defaultValue: l,
    rules: d,
    containerStyle: c = {},
    labelStyle: u = {},
    labelTextStyle: p = {},
    row: h = !1,
    template: m = "1fr",
    gap: f = "10px",
    onChange: g,
    ...y
}) {
    return e.jsx(Mm, {
        name: t,
        control: a,
        defaultValue: l || !1,
        rules: d,
        render: ({
            field: a
        }) => e.jsxs("div", {
            children: [n && e.jsx(D, {
                children: e.jsx(o, {
                    sx: p,
                    children: n
                })
            }), e.jsx(V, {
                ...a,
                "aria-label": t,
                sx: {
                    display: "grid",
                    gridTemplateColumns: m,
                    width: "100%",
                    gap: f,
                    ...h && {
                        display: "flex",
                        flexDirection: "row"
                    },
                    ...c
                },
                name: t,
                onChange: e => {
                    a.onChange(e), g?.(e, a.value)
                },
                children: r.map(t => e.jsx(A, {
                    value: t.value,
                    disabled: t?.disabled,
                    sx: {
                        mr: "30px"
                    },
                    control: e.jsx(C, {
                        inputProps: {
                            "aria-label": t.label
                        },
                        ...y
                    }),
                    label: e.jsx(o, {
                        sx: {
                            ...u,
                            color: t?.disabled ? "#B6B6B6" : "#232425"
                        },
                        children: t.label
                    })
                }, t.label))
            })]
        })
    })
}
const c5 = [{
        id: 5,
        name: "Saturday",
        name_ar: "السبت"
    }, {
        id: 6,
        name: "Sunday",
        name_ar: "الأحد"
    }, {
        id: 0,
        name: "Monday",
        name_ar: "الإثنين"
    }, {
        id: 1,
        name: "Tuesday",
        name_ar: "الثلاثاء"
    }, {
        id: 2,
        name: "Wednesday",
        name_ar: "الأربعاء"
    }, {
        id: 3,
        name: "Thursday",
        name_ar: "الخميس"
    }, {
        id: 4,
        name: "Friday",
        name_ar: "الجمعة"
    }],
    u5 = e => v1().shape({
        name_en: a1().required(e("facilitiesBooking.facilityName_required")),
        name_ar: a1().required(e("facilitiesBooking.facilityName_required")),
        start_time: a1().nullable().when("working_hours_type", {
            is: O6.CUSTOM,
            then: e => e.required("fieldRequired")
        }),
        end_time: a1().nullable().when("working_hours_type", {
            is: O6.CUSTOM,
            then: e => e.required("fieldRequired")
        }),
        approved: qX(),
        images: WX().nullable(),
        description: a1(),
        capacity_limit: a1(),
        reservation_duration: o1().nullable().when("booking_type", {
            is: "private",
            then: e => e.required("fieldRequired")
        }),
        gender: a1().required(),
        working_hours_type: a1().required("fieldRequired"),
        age: WX().nullable(),
        capacity: o1().integer("Capacity must be an integer").nullable().when("capacity_limit", {
            is: O6.CUSTOM,
            then: e => e.min(1, "noZero").required("fieldRequired")
        }),
        booking_type: a1().required("fieldRequired").typeError("fieldRequired"),
        complex_id: a1().required(),
        days: x1().min(1, "fieldRequired").required("fieldRequired").test("atLeastOne", "atLeastOne", e => {
            const t = e.filter(e => e);
            return 0 !== t?.length
        })
    }),
    p5 = {
        name_en: "",
        name_ar: "",
        images: null,
        description: "",
        start_time: null,
        end_time: null,
        booking_type: "",
        capacity: null,
        approved: !1,
        gender: O6.BOTH,
        age: null,
        reservation_duration: 30,
        days: [],
        working_hours_type: O6.ALL_DAY,
        capacity_limit: O6.NO_CAPACITY
    };

function h5({
    name: t,
    label: n,
    control: r,
    checked: a,
    errors: i,
    onFocus: o,
    defaultValue: s,
    rules: l,
    stringvalue: d,
    onChange: c,
    style: p = {},
    ...h
}) {
    return e.jsx(Mm, {
        name: t,
        control: r,
        render: ({
            field: r
        }) => e.jsx(A, {
            sx: {
                backgroundColor: e => r.value ? u(e.palette.primary.main, .2) : "#fff",
                textAlign: "center",
                display: "inline-block",
                p: "12px 24px",
                border: e => `1px solid ${r.value?e.palette.primary.main:"#E3E3E3"}\n            `,
                borderRadius: "8px",
                mr: "24px",
                mb: "24px",
                textTransform: "capitalize",
                transition: "0.2s ease all",
                "&:hover": {
                    backgroundColor: "#f1f1f1"
                },
                "& .MuiFormControlLabel-label": {
                    fontSize: "14px"
                },
                ...p
            },
            control: e.jsx(S, {
                checked: a || r.value,
                onBlur: r.onBlur,
                onChange: (e, t) => {
                    c?.(e, t), r.onChange(!!t && d), r.onBlur()
                },
                name: t,
                ...h,
                style: {
                    display: "none"
                }
            }),
            label: n
        }),
        defaultValue: s || !1,
        rules: l
    })
}

function m5({
    workingDays: t,
    control: n,
    errors: r
}) {
    const {
        t: a,
        i18n: i
    } = Gn();
    return e.jsxs(e.Fragment, {
        children: [e.jsx(rP, {
            variant: "h4",
            s: 18,
            children: a("contacts.Set Working Days")
        }), e.jsx(Le, {
            sx: {
                mt: "32px",
                ml: "12px"
            },
            children: t.map(t => e.jsx(h5, {
                control: n,
                errors: r,
                stringvalue: t,
                onChange: () => {
                    n.setError("days", void 0)
                },
                name: `days.${t.id}`,
                label: "ar" === i.language ? t?.name_ar : bh.startCase(t?.name?.substring(0, 3))
            }, t.id))
        }), r?.days && e.jsx(rP, {
            sx: {
                color: "#f50057"
            },
            s: "12",
            light: !0,
            children: a(r?.days?.root?.message)
        })]
    })
}
const f5 = ({
        value: t,
        onChange: n,
        min: r = 0,
        max: a = 1 / 0,
        dynamicWidth: i,
        renderValue: o,
        placeholderText: s,
        changeAmount: l = 1,
        controlsDisabled: d = !1
    }) => {
        const [c, u] = Dt.useState(r);
        Dt.useEffect(() => {
            n?.(c)
        }, [c]), Dt.useEffect(() => {
            t && u(+t)
        }, [t]);
        return e.jsxs(cP, {
            row: !0,
            xbetween: !0,
            ycenter: !0,
            sx: {
                width: i ? "inherit" : "360px",
                padding: "4px",
                borderRadius: "8px",
                border: " 1px solid #E3E3E3"
            },
            children: [e.jsx(w, {
                "aria-label": "decrement",
                disabled: d || c <= r,
                onClick: () => u(e => e > r ? e - l : e),
                sx: {
                    color: "#000"
                },
                children: e.jsx(Af, {})
            }), e.jsx(rP, {
                "data-testid": "counter-content",
                s: 16,
                light: !0,
                children: o ? o() : o || null == t ? c || (e.jsx(rP, {
                    s: 16,
                    color: "#969798",
                    light: !0,
                    children: s
                }) ?? "0") : c
            }), e.jsx(w, {
                "aria-label": "increment",
                disabled: d || c >= a,
                onClick: () => u(e => e < a ? e + l : e),
                sx: {
                    color: "#000"
                },
                children: e.jsx(jf, {})
            })]
        })
    },
    g5 = ({
        name: t,
        label: n = "",
        control: r,
        errors: a,
        dynamicWidth: i,
        defaultValue: o,
        rules: s,
        ...l
    }) => {
        const {
            t: d
        } = Gn();
        return e.jsx(Mm, {
            name: t,
            control: r,
            defaultValue: o,
            rules: s,
            render: ({
                field: r
            }) => e.jsxs(cP, {
                column: !0,
                sx: {
                    gap: 4
                },
                children: [e.jsx(rP, {
                    variant: "caption",
                    s: 14,
                    sx: {
                        mb: "-4px",
                        fontWeight: 400,
                        color: "#525451",
                        textWrap: "nowrap"
                    },
                    children: n
                }), e.jsx(f5, {
                    ...r,
                    ...l,
                    dynamicWidth: i
                }), a?.[t] && e.jsx(rP, {
                    s: 14,
                    light: !0,
                    color: "red",
                    children: `${d(a[t]?.message)}`
                })]
            })
        })
    };

function y5({
    capacityLimitOptions: t,
    capacity_limit: n,
    control: r,
    errors: a,
    capacity: i
}) {
    const {
        t: o
    } = Gn();
    return e.jsxs(e.Fragment, {
        children: [e.jsx(rP, {
            variant: "h4",
            s: 18,
            children: o("facilitiesBooking.capacity_title")
        }), e.jsx(rP, {
            s: 14,
            sx: {
                mb: "22px"
            },
            light: !0,
            gray: !0,
            children: o("facilitiesBooking.capacity_caption")
        }), e.jsx(lP, {
            md: 12,
            children: e.jsx(d5, {
                name: "capacity_limit",
                labels: t,
                control: r,
                errors: a,
                row: !0,
                label: !0,
                color: "primary",
                gap: "60px",
                labelStyle: v5
            })
        }), n === O6.CUSTOM && e.jsxs(lP, {
            xs: 12,
            md: 6,
            sx: {
                mt: "16px"
            },
            children: [e.jsx(rP, {
                variant: "h4",
                s: 12,
                sx: {
                    mb: "8px"
                },
                light: !0,
                children: o("facilitiesBooking.limits")
            }), e.jsx(g5, {
                control: r,
                errors: a,
                value: i ? Number(i) : null,
                name: "capacity",
                placeholderText: o("facilitiesBooking.limits_placeholder")
            })]
        })]
    })
}
const v5 = {
    fontSize: "17px !important",
    fontWeight: "500 !important",
    color: "#232425"
};

function _5({
    form: t
}) {
    const {
        setValue: n,
        formState: {
            errors: r
        },
        getValues: a
    } = t, {
        t: i
    } = Gn(), o = P6(i).facilityTypes, [s, l] = Dt.useState(!1), [d, c] = Dt.useState("");
    Dt.useEffect(() => {
        c(R6(o, a("booking_type")))
    }, [a("booking_type")]);
    return e.jsxs(e.Fragment, {
        children: [e.jsx(rP, {
            s: "14",
            light: !0,
            sx: {
                mb: "10px",
                fontWeight: 400,
                fontSize: "14px",
                color: "#525451"
            },
            children: i("facilitiesBooking.facilityType")
        }), s ? e.jsx(cP, {
            children: e.jsxs(cP, {
                sx: {
                    border: "2px solid #E2E8F0",
                    borderRadius: "8px"
                },
                onClick: () => l(!s),
                children: [e.jsxs(cP, {
                    xbetween: !0,
                    sx: {
                        p: "12.5px 14px",
                        pb: 0,
                        cursor: "pointer"
                    },
                    children: [e.jsx(rP, {
                        disabled: !0,
                        variant: "caption",
                        sx: {
                            color: "#969798",
                            fontWeight: "400"
                        },
                        s: "16",
                        children: i("facilitiesBooking.selectFacilityType")
                    }), e.jsx(Zf, {})]
                }), e.jsx(cP, {
                    children: e.jsx(ge, {
                        children: o.map(r => e.jsx(Ze, {
                            component: "button",
                            onClick: () => (e => {
                                n("booking_type", e.value), c(e.label), t.trigger("booking_type")
                            })(r),
                            sx: {
                                width: "100%",
                                textTransform: "capitalize",
                                px: "4rem"
                            },
                            children: e.jsx(Ue, {
                                primary: r.label,
                                sx: {
                                    "& .MuiTypography-root": {
                                        fontWeight: "400",
                                        fontSize: "16px"
                                    }
                                }
                            })
                        }, r.value))
                    })
                })]
            })
        }) : e.jsxs(cP, {
            onClick: () => l(!0),
            sx: {
                borderRadius: "8px",
                py: "0.4rem",
                "&.MuiInputBase-root": {
                    border: "3px solid " + (r?.booking_type ? "#f50057" : "#E2E8F0")
                },
                "& > .MuiOutlinedInput-notchedOutline": {
                    border: "none"
                },
                display: "flex",
                justifyContent: "space-between",
                border: r?.booking_type ? "1px solid #f50057" : "2px solid #E2E8F0",
                p: "12.5px 14px",
                cursor: "pointer"
            },
            children: [d ? e.jsx(rP, {
                light: !0,
                s: "16",
                children: d
            }) : e.jsx(rP, {
                sx: {
                    color: "#969798",
                    fontWeight: "400"
                },
                s: "16",
                children: i("facilitiesBooking.selectFacilityType")
            }), e.jsx(Bf, {})]
        }), r?.booking_type && e.jsx(rP, {