        Dt.useEffect(() => {
            const r = {
                    ...EQ.parse(s.toString())
                },
                a = {
                    [`${i}search`]: e && null != u.search ? u.search : "",
                    [`${i}filter`]: t ? u.filter : void 0,
                    [`${i}sort`]: n && u.sort?.sortBy ? u.sort : void 0,
                    [`${i}page`]: u.page
                };
            for (const e of Object.keys(a)) {
                const t = a[e];
                r[e] = t
            }
            const d = EQ.format(r);
            d.toString() !== s.toString() && (o ? l(d, {
                replace: d.get(`${i}page`) === s.get(`${i}page`)
            }) : window.history.replaceState({}, "", `?${d.toString()}`))
        }, [JSON.stringify(u)]);
        const h = Dt.useCallback(e => {
                p({
                    type: "SET_SEARCH",
                    payload: e
                })
            }, []),
            m = Dt.useCallback(e => {
                p({
                    type: "SET_FILTER",
                    payload: e
                })
            }, []),
            f = Dt.useCallback(e => {
                p({
                    type: "SET_SORT",
                    payload: e
                })
            }, []),
            g = Dt.useCallback(e => {
                p({
                    type: "SET_PAGE",
                    payload: e
                })
            }, []);
        return {
            state: u,
            setSearch: h,
            setFilter: m,
            setSort: f,
            setPage: g
        }
    },
    OQ = yF;

function PQ() {
    const {
        type: t
    } = qt(), {
        t: n
    } = Gn(), {
        state: {
            filter: r,
            search: a,
            page: i
        },
        setFilter: o,
        setSearch: s,
        setPage: l
    } = AQ({
        enableSort: !1,
        defaultFilter: {
            type: t === uU.Tenant ? m$.INDIVIDUAL : t,
            showInactive: !1
        }
    }), d = "true" === r?.showInactive || !0 === r?.showInactive, [c, u] = Dt.useState(d), p = () => {
        u(!c), m(!c), o({
            ...r,
            showInactive: !c
        })
    }, h = {
        [uU.Manager]: {
            actionButtons: () => e.jsxs(cP, {
                fullWidth: !0,
                row: !0,
                ycenter: !0,
                gap: 5,
                sx: {
                    mt: {
                        xs: 4,
                        sm: 0
                    },
                    mx: {
                        xs: 0,
                        sm: 8
                    },
                    width: {
                        xs: "100%"
                    },
                    flexDirection: {
                        xs: "column-reverse",
                        sm: "row"
                    },
                    alignItems: {
                        xs: "stretch",
                        sm: "center"
                    },
                    gap: {
                        xs: 4,
                        sm: 0
                    }
                },
                children: [e.jsx(dP, {
                    onClick: p,
                    sx: {
                        mx: {
                            xs: 0,
                            sm: 8
                        },
                        width: "100%"
                    },
                    variant: "outlined",
                    color: "primary",
                    children: n(c ? "showActiveList" : "showInactiveList")
                }), e.jsx(oi, {
                    I: qI.Create,
                    this: $I.Managers,
                    children: e.jsxs(r$, {
                        sx: {
                            width: "100%"
                        },
                        component: Wt,
                        to: `/contacts/${uU.Manager}/form`,
                        children: [e.jsx(jf, {}), " ", n(`contacts.roles.NEW_${t.toUpperCase()}`)]
                    })
                })]
            }),
            headers: [n("requests.Name"), n("requests.phoneNumber"), n("contacts.Managers Type"), ""],
            fetcher: () => W$({
                search: a,
                page: i,
                userType: "admins",
                active: c ? 0 : 1
            })
        },
        [uU.Owner]: {
            actionButtons: () => e.jsxs(cP, {
                fullWidth: !0,
                row: !0,
                ycenter: !0,
                gap: 5,
                sx: {
                    mt: {
                        xs: 4,
                        sm: 0
                    },
                    mx: {
                        xs: 0,
                        sm: 8
                    },
                    width: {
                        xs: "100%"
                    },
                    flexDirection: {
                        xs: "column-reverse",
                        sm: "row"
                    },
                    alignItems: {
                        xs: "stretch",
                        sm: "center"
                    },
                    gap: {
                        xs: 4,
                        sm: 0
                    }
                },
                children: [e.jsx(dP, {
                    onClick: p,
                    sx: {
                        mx: {
                            xs: 0,
                            sm: 8
                        },
                        width: "100%"
                    },
                    variant: "outlined",
                    color: "primary",
                    children: n(c ? "showActiveList" : "showInactiveList")
                }), e.jsx(oi, {
                    I: qI.Create,
                    this: $I.Owners,
                    children: e.jsxs(r$, {
                        sx: {
                            width: "100%"
                        },
                        component: Wt,
                        to: `/contacts/${t}/form`,
                        children: [e.jsx(jf, {}), " ", n(`contacts.roles.NEW_${t}`)]
                    })
                })]
            }),
            headers: [n("requests.Name"), n("requests.phoneNumber"), n("headers.unitNum"), ""],
            fetcher: () => W$({
                search: a,
                page: i,
                userType: "owners",
                active: c ? 0 : 1
            })
        },
        [uU.Tenant]: {
            actionButtons: () => e.jsx(cP, {
                fullWidth: !0,
                row: !0,
                ycenter: !0,
                gap: 5,
                sx: {
                    mt: {
                        xs: 4,
                        sm: 0
                    },
                    mx: {
                        xs: 0,
                        sm: 8
                    },
                    width: {
                        xs: "100%"
                    },
                    flexDirection: {
                        xs: "column-reverse",
                        sm: "row"
                    },
                    alignItems: {
                        xs: "stretch",
                        sm: "center"
                    },
                    gap: {
                        xs: 4,
                        sm: 0
                    }
                },
                children: e.jsx(oi, {
                    I: qI.Create,
                    this: $I.Tenants,
                    children: !c && e.jsxs(e.Fragment, {
                        children: [e.jsx(dP, {
                            onClick: p,
                            sx: {
                                mx: {
                                    xs: 0,
                                    sm: 8
                                },
                                width: "100%"
                            },
                            variant: "outlined",
                            color: "primary",
                            children: n(c ? "showActiveList" : "contacts.deactivatedUser")
                        }), e.jsx(aG, {})]
                    })
                })
            }),
            headers: r?.type === m$.INDIVIDUAL ? [n("requests.Name"), n("requests.phoneNumber"), n("headers.unitNum"), n("acceptInvite"), ""] : [n("requests.Name"), n("contacts.companyRegistrationNo"), n("contacts.taxIdentifierNo"), n("contacts.primaryUser"), n("contacts.relatedCompaniesNo"), ""],
            fetcher: () => r?.type === m$.INDIVIDUAL ? Z$({
                search: a,
                page: i,
                active: c ? 0 : 1
            }) : q$({
                search: a,
                page: i,
                active: c ? 0 : 1
            })
        },
        [uU.ServiceProfessional]: {
            actionButtons: () => e.jsxs(cP, {
                fullWidth: !0,
                row: !0,
                ycenter: !0,
                gap: 5,
                sx: {
                    mt: {
                        xs: 4,
                        sm: 0
                    },
                    mx: {
                        xs: 0,
                        sm: 8
                    },
                    width: {
                        xs: "100%"
                    },
                    flexDirection: {
                        xs: "column-reverse",
                        sm: "row"
                    },
                    alignItems: {
                        xs: "stretch",
                        sm: "center"
                    },
                    gap: {
                        xs: 4,
                        sm: 0
                    }
                },
                children: [e.jsx(dP, {
                    onClick: p,
                    sx: {
                        mx: {
                            xs: 0,
                            sm: 8
                        },
                        width: "100%"
                    },
                    variant: "outlined",
                    color: "primary",
                    children: n(c ? "showActiveList" : "showInactiveList")
                }), e.jsx(oi, {
                    I: qI.Create,
                    this: $I.ServiceProfessionals,
                    children: e.jsxs(r$, {
                        sx: {
                            width: "100%"
                        },
                        component: Wt,
                        to: `/contacts/${t}/form`,
                        children: [e.jsx(jf, {}), " ", n(`contacts.roles.NEW_${t}`)]
                    })
                })]
            }),
            headers: [n("requests.Name"), n("jobTitle"), n("requests.phoneNumber"), ""],
            fetcher: () => W$({
                search: a,
                page: i,
                userType: "professionals",
                active: c ? 0 : 1
            })
        }
    }, m = e => {
        o({
            ...r,
            showInactive: e
        })
    }, {
        data: f,
        isLoading: g
    } = tl([OQ, t, a, r, i, c, r?.selectedTab], async () => h[t]?.fetcher?.(), {
        refetchOnMount: "always",
        staleTime: 1,
        useErrorBoundary: !1
    }), {
        data: y
    } = tl(["TOTAL_TENANTS"], $$, {
        refetchOnMount: "always",
        cacheTime: 0,
        useErrorBoundary: !1,
        enabled: t === uU.Tenant
    });
    return {
        data: f?.data?.list ?? f,
        page: i,
        search: a,
        handleSearch: s,
        isLoading: g,
        handlePage: l,
        count: f?.data?.paginator?.last_page ?? f?.count,
        total: f?.data?.paginator?.total ?? y?.user_count,
        activeContact: h[t],
        setSelectedTab: e => {
            s(""), l(1), o({
                ...r,
                type: e
            })
        },
        selectedTab: r?.type,
        showInactive: d,
        toggleInactive: m
    }
}

function IQ({
    handleBackAction: t,
    sx: n
}) {
    const {
        t: r,
        i18n: a
    } = Gn(), i = Ft();
    return e.jsx(l, {
        variant: "outlined",
        color: "inherit",
        onClick: t || (() => i(-1)),
        sx: {
            width: "fit-content",
            ...n,
            borderColor: "#CACACA"
        },
        startIcon: "ar" === a?.language ? e.jsx(nt, {
            color: "inherit"
        }) : e.jsx(rt, {}),
        children: e.jsx(rP, {
            s: 14,
            children: r("common.back")
        })
    })
}
const FQ = ({
        search: t,
        title: n,
        extraAction: r,
        actionButton: i,
        filtering: o,
        isBack: s,
        tabs: l,
        isTabs: d
    }) => {
        const c = Ft(),
            {
                type: u
            } = qt();
        return e.jsxs(e.Fragment, {
            children: [s && e.jsxs(sP, {
                justifyContent: "space-between",
                alignItems: "center",
                spacing: 4,
                children: [e.jsx(lP, {
                    children: e.jsx(IQ, {
                        handleBackAction: () => {
                            c(-1)
                        }
                    })
                }), e.jsx(lP, {
                    children: e.jsxs(a, {
                        sx: {
                            display: "grid",
                            gridTemplateColumns: {
                                xs: "1fr",
                                md: "repeat(3,max-content)",
                                lg: "repeat(3,max-content)"
                            },
                            alignItems: "center",
                            gap: "15px"
                        },
                        children: [t && t, o, i]
                    })
                })]
            }), e.jsxs(sP, {
                justifyContent: "space-between",
                alignItems: "center",
                sx: {
                    mt: 8
                },
                spacing: 4,
                children: [e.jsx(lP, {
                    children: e.jsx(e.Fragment, {
                        children: n
                    })
                }), e.jsx(lP, {
                    children: e.jsxs(a, {
                        sx: {
                            display: "grid",
                            gridTemplateColumns: {
                                xs: "1fr",
                                md: "repeat(3,max-content)",
                                lg: "repeat(3,max-content)"
                            },
                            alignItems: "center",
                            gap: "15px"
                        },
                        children: [e.jsx(e.Fragment, {
                            children: r
                        }), !s && e.jsxs(e.Fragment, {
                            children: [t && t, o, i]
                        })]
                    })
                })]
            }), l]
        })
    },
    HQ = ({
        page: t,
        count: n,
        handler: r
    }) => n < 2 || void 0 === n ? e.jsx(e.Fragment, {}) : e.jsx(sP, {
        justifyContent: "flex-end",
        children: e.jsx(at, {
            siblingCount: 0,
            boundaryCount: 1,
            page: t,
            count: n,
            onChange: (e, t) => {
                r(t)
            },
            color: "primary",
            variant: "outlined",
            shape: "rounded",
            sx: {
                ".MuiPagination-outlined": {
                    color: "red"
                },
                ".MuiPagination-ul": {
                    flexWrap: "nowrap"
                },
                ...NQ.ul
            }
        })
    }),
    NQ = {
        ul: {
            "& .MuiPaginationItem-root": {
                color: "#232425",
                fontWeight: 400
            },
            "& .Mui-selected": {
                color: "white",
                backgroundColor: "#2E3032"
            }
        }
    },
    RQ = Dt.memo(({
        search: t,
        handleSearch: n,
        sx: r = {},
        isGrayIcon: a = !1,
        containerStyle: i = {},
        disabled: o = !1,
        height: l,
        placeholder: d,
        iconSx: c
    }) => {
        const [u, p] = Dt.useState(!1), {
            t: h
        } = Gn(), [m, f] = Dt.useState(t), g = s(), y = ce(g.breakpoints.down("sm"));
        Dt.useEffect(() => {
            f(t)
        }, [t]);
        const v = Dt.useMemo(() => bh.debounce(e => n(e), 1e3), []);
        return e.jsx(e.Fragment, {
            children: e.jsx(cP, {
                sx: {
                    height: 40,
                    ...i
                },
                children: e.jsx(E, {
                    sx: {
                        mr: 4,
                        "& .MuiOutlinedInput-input": {
                            py: "7px",
                            height: l || null
                        },
                        ...r
                    },
                    margin: "none",
                    variant: "outlined",
                    placeholder: d || h("dashboard.search"),
                    value: m,
                    autoFocus: !y,
                    disabled: o,
                    onChange: e => {
                        f(e.target.value), v(e.target.value)
                    },
                    inputProps: {
                        "aria-label": "search"
                    },
                    InputProps: {
                        startAdornment: e.jsx(j, {
                            position: "end",
                            children: a ? e.jsx(ZH, {
                                sx: c
                            }) : e.jsx(yh, {
                                color: a ? "" : "primary",
                                sx: {
                                    cursor: "pointer",
                                    fill: a && "#969798"
                                }
                            })
                        })
                    }
                })
            })
        })
    });

function YQ({
    className: t = ""
}) {
    const {
        t: n
    } = Gn();
    return e.jsx("div", {
        className: t,
        style: {
            display: "flex",
            justifyContent: "center",
            width: "100%",
            marginTop: "5rem"
        },
        children: e.jsxs("div", {
            children: [e.jsx("svg", {
                className: "h-full",
                width: 184,
                height: 152,
                viewBox: "0 0 184 152",
                xmlns: "http://www.w3.org/2000/svg",
                children: e.jsxs("g", {
                    fill: "none",
                    fillRule: "evenodd",
                    children: [e.jsxs("g", {
                        transform: "translate(24 31.67)",
                        children: [e.jsx("ellipse", {
                            cx: "67.797",
                            cy: "106.89",
                            rx: "67.797",
                            ry: "12.668",
                            style: {
                                color: "#999",
                                opacity: .75,
                                fill: "currentColor"
                            }
                        }), e.jsx("path", {
                            d: "M122.034 69.674L98.109 40.229c-1.148-1.386-2.826-2.225-4.593-2.225h-51.44c-1.766 0-3.444.839-4.592 2.225L13.56 69.674v15.383h108.475V69.674z",
                            style: {
                                color: "#bbb",
                                fill: "currentColor"
                            }
                        }), e.jsx("path", {
                            d: "M101.537 86.214L80.63 61.102c-1.001-1.207-2.507-1.867-4.048-1.867H31.724c-1.54 0-3.047.66-4.048 1.867L6.769 86.214v13.792h94.768V86.214z",
                            style: {
                                color: "#bbb",
                                fill: "currentColor"
                            },
                            transform: "translate(13.56)"
                        }), e.jsx("path", {
                            d: "M33.83 0h67.933a4 4 0 0 1 4 4v93.344a4 4 0 0 1-4 4H33.83a4 4 0 0 1-4-4V4a4 4 0 0 1 4-4z",
                            style: {
                                color: "white",
                                fill: "currentColor"
                            }
                        }), e.jsx("path", {
                            d: "M42.678 9.953h50.237a2 2 0 0 1 2 2V36.91a2 2 0 0 1-2 2H42.678a2 2 0 0 1-2-2V11.953a2 2 0 0 1 2-2zM42.94 49.767h49.713a2.262 2.262 0 1 1 0 4.524H42.94a2.262 2.262 0 0 1 0-4.524zM42.94 61.53h49.713a2.262 2.262 0 1 1 0 4.525H42.94a2.262 2.262 0 0 1 0-4.525zM121.813 105.032c-.775 3.071-3.497 5.36-6.735 5.36H20.515c-3.238 0-5.96-2.29-6.734-5.36a7.309 7.309 0 0 1-.222-1.79V69.675h26.318c2.907 0 5.25 2.448 5.25 5.42v.04c0 2.971 2.37 5.37 5.277 5.37h34.785c2.907 0 5.277-2.421 5.277-5.393V75.1c0-2.972 2.343-5.426 5.25-5.426h26.318v33.569c0 .617-.077 1.216-.221 1.789z",
                            style: {
                                color: "#bbb",
                                fill: "currentColor"
                            }
                        })]
                    }), e.jsx("path", {
                        d: "M149.121 33.292l-6.83 2.65a1 1 0 0 1-1.317-1.23l1.937-6.207c-2.589-2.944-4.109-6.534-4.109-10.408C138.802 8.102 148.92 0 161.402 0 173.881 0 184 8.102 184 18.097c0 9.995-10.118 18.097-22.599 18.097-4.528 0-8.744-1.066-12.28-2.902z",
                        style: {
                            color: "#bbb",
                            fill: "currentColor"
                        }
                    }), e.jsxs("g", {
                        style: {
                            color: "white",
                            fill: "currentColor"
                        },
                        transform: "translate(149.65 15.383)",
                        children: [e.jsx("ellipse", {
                            cx: "20.654",
                            cy: "3.167",
                            rx: "2.849",
                            ry: "2.815"
                        }), e.jsx("path", {
                            d: "M5.698 5.63H0L2.898.704zM9.259.704h4.985V5.63H9.259z"
                        })]
                    })]
                })
            }), e.jsx(a, {
                sx: {
                    textAlign: "center",
                    mt: 8
                },
                children: e.jsx(o, {
                    children: n("dashboard.nodata")
                })
            })]
        })
    })
}

function BQ({
    Header: t,
    Footer: n,
    Section: r,
    AfterHeader: a,
    BeforeFooter: i
}) {
    return e.jsxs(Ae, {
        maxWidth: !1,
        children: [t, a, r, i, n]
    })
}

function zQ({
    sizes: t,
    data: n,
    loading: r,
    renderItem: a,
    SectionWrapperComponent: i = Vt.Fragment,
    ...o
}) {
    return r ? e.jsx(hP, {}) : e.jsx(e.Fragment, {
        children: e.jsx(BQ, {
            Section: n?.length ? e.jsx(i, {
                children: n?.map(n => e.jsx(lP, {
                    xs: 12,
                    sm: 12,
                    md: t?.md || 6,
                    lg: t?.lg || 6,
                    xl: t?.xl || 4,
                    children: e.jsx(Ne, {
                        sx: {
                            height: "100%"
                        },
                        children: a({
                            item: n
                        })
                    })
                }, n.id))
            }) : e.jsx(YQ, {}),
            ...o
        })
    })
}

function UQ({
    children: t
}) {
    return e.jsx(a, {
        sx: {
            flexGrow: 1
        },
        children: e.jsx(sP, {
            spacing: 12,
            sx: {
                mt: 0,
                mb: 12
            },
            children: t
        })
    })
}
var WQ, ZQ = {};

function qQ() {
    if (WQ) return ZQ;
    WQ = 1;
    var e = h();
    Object.defineProperty(ZQ, "__esModule", {
        value: !0
    }), ZQ.default = void 0;
    var t = e(jp()),
        n = m();
    return ZQ.default = (0, t.default)((0, n.jsx)("path", {
        d: "M19 3H5c-1.11 0-2 .9-2 2v14c0 1.1.89 2 2 2h14c1.11 0 2-.9 2-2V5c0-1.1-.89-2-2-2m-9 14-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8z"
    }), "CheckBox"), ZQ
}
const $Q = It(qQ());
var GQ, KQ = {};

function QQ() {
    if (GQ) return KQ;
    GQ = 1;
    var e = h();
    Object.defineProperty(KQ, "__esModule", {
        value: !0
    }), KQ.default = void 0;
    var t = e(jp()),
        n = m();
    return KQ.default = (0, t.default)((0, n.jsx)("path", {
        d: "M19 5v14H5V5zm0-2H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2"
    }), "CheckBoxOutlineBlank"), KQ
}
const JQ = It(QQ()),
    XQ = ({
        item: t,
        ...n
    }) => {
        const {
            selected: r,
            setSelected: i
        } = n;
        return e.jsxs(et, {
            sx: {
                cursor: "pointer",
                position: "relative",
                border: "2px solid",
                borderColor: e => r?.id === t.id ? e?.palette?.primary?.main : "transparent",
                borderRadius: "15px",
                boxSizing: "border-box",
                transition: " 0.2s ease-in-out",
                "&:hover": {
                    border: "2px solid",
                    borderColor: e => e?.palette?.primary?.main
                }
            },
            onClick: () => {
                i(t)
            },
            children: [e.jsx(a, {
                sx: {
                    position: "absolute",
                    top: "10px",
                    right: "10px"
                },
                children: t.id === r?.id ? e.jsx($Q, {
                    color: "primary"
                }) : e.jsx(JQ, {
                    sx: {
                        color: Ge[500]
                    }
                })
            }), e.jsxs(sP, {
                alignItems: "center",
                children: [e.jsx(lP, {
                    sx: {
                        marginRight: "10px"
                    },
                    children: e.jsxs(f, {
                        sx: {
                            width: 56,
                            height: 56,
                            color: "black",
                            backgroundColor: "rgba(0, 142, 165, 0.08);"
                        },
                        children: [t?.name?.[0]?.toUpperCase(), t?.name?.split(" ")?.[1]?.[0]?.toUpperCase()]
                    })
                }), e.jsxs(lP, {
                    children: [e.jsx(o, {
                        component: "h6",
                        variant: "h6",
                        sx: {
                            overflow: "hidden",
                            textOverflow: "ellipsis",
                            whiteSpace: "nowrap"
                        },
                        children: hZ(t?.name)
                    }), e.jsx(o, {
                        variant: "subtitle1",
                        sx: {
                            fontWeight: 400
                        },
                        children: t?.full_phone_number
                    })]
                })]
            })]
        })
    },
    eJ = ({
        data: t,
        title: n,
        footer: r,
        selectOptions: a
    }) => e.jsx(zQ, {
        data: t,
        SectionWrapperComponent: UQ,
        renderItem: ({
            item: t
        }) => e.jsx(XQ, {
            item: t,
            ...a
        }),
        Header: n,
        Footer: r
    }),
    tJ = async ({
        id: e,
        data: t
    }) => await co(`/api-management/new/complaints/${e}/cancel`, t), nJ = "ASSIGN_ISSUE", rJ = () => {
        const {
            id: t
        } = qt(), n = Ft(), {
            data: r,
            page: a,
            count: i,
            search: s,
            total: l,
            dispatch: d,
            handleSearch: c
        } = PQ(), {
            t: u
        } = Gn(), [p, h] = Dt.useState();
        return e.jsx(eJ, {
            selectOptions: {
                selected: p,
                setSelected: h
            },
            data: r,
            title: e.jsx(FQ, {
                isBack: !0,
                title: e.jsxs(e.Fragment, {
                    children: [e.jsx(o, {
                        variant: "h4",
                        children: u("issues.select professional")
                    }), e.jsxs(o, {
                        variant: "subtitle1",
                        sx: {
                            fontWeight: "400",
                            color: Ge[600]
                        },
                        children: [u("common.total"), ": ", l]
                    })]
                }),
                search: e.jsx(RQ, {
                    search: s,
                    handleSearch: c
                }),
                filtering: e.jsx(e.Fragment, {}),
                actionButton: e.jsx(e.Fragment, {}),
                extraAction: e.jsx(e.Fragment, {
                    children: " "
                })
            }),
            footer: e.jsxs(e.Fragment, {
                children: [e.jsx(lP, {
                    sx: {
                        display: "grid",
                        gridTemplateColumns: {
                            xs: "1fr",
                            md: "repeat(2,1fr)",
                            xl: "repeat(3,1fr)"
                        },
                        gridGap: 30,
                        my: "12px"
                    },
                    children: e.jsx(a$, {
                        name: nJ,
                        variant: "contained",
                        onClick: async () => {
                            fq(nJ, !0);
                            try {
                                await (async ({
                                    id: e,
                                    assigned_id: t
                                }) => await co(`/api-management/new/complaints/${e}/assign`, {
                                    assigned_id: t
                                }))({
                                    id: t,
                                    assigned_id: p?.id
                                }), Zi.success(u("common.success")), n("/dashboard/issues")
                            } catch (e) {
                                fq(nJ, !1), Lo(e, {}, !0)
                            }
                            fq(nJ, !1)
                        },
                        children: u("editForm.assign")
                    })
                }), e.jsx(HQ, {
                    page: a,
                    count: i,
                    handler: e => d({
                        type: "PAGE",
                        payload: e
                    })
                })]
            })
        })
    }, aJ = {
        1: "New complaint",
        3: "Resolved",
        4: "Cancelled"
    }, iJ = {
        1: {
            color: lt[500],
            backgroundColor: lt[50]
        },
        2: {
            color: st[800],
            backgroundColor: ot[50]
        },
        3: {
            color: it[500],
            backgroundColor: it[50]
        },
        4: {
            color: Ge[600],
            backgroundColor: Ge[100]
        }
    }, oJ = {
        sort: "0",
        search: "",
        page: 1,
        filter: "0",
        status: "",
        select: !1,
        selected: [],
        selectedTab: 1,
        showInactive: !1,
        is_paginate: 1
    }, sJ = (e = oJ, t) => {
        switch (t.type) {
            case "SORT":
                return {
                    ...e, sort: t.payload
                };
            case "SEARCH":
                return {
                    ...e, search: t.payload
                };
            case "PAGE":
                return {
                    ...e, page: t.payload
                };
            case "FILTER":
                return {