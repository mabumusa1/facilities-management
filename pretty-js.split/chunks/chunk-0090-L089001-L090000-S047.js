                x({
                    ...h,
                    [yU.TYPE]: []
                })
            },
            isOpen: u,
            setIsOpen: p,
            fetcher: ({
                page: e,
                search: t
            }) => SU(h?.[yU.SUB_CATEGORY]?.[0]?.id, {
                page: e,
                search: t
            }),
            refetchKey: `${HH}.${h?.[yU.CATEGORY]}.${h?.[yU.SUB_CATEGORY]?.map(e=>e.id)}`,
            isMultiSelect: !1,
            sx: t4.selectField,
            placeholder: n("announcements.select type"),
            title: n("selectRequestType"),
            searchPlaceholder: n("contacts.searchPlaceholder"),
            noDataTitle: n("noTypeFound"),
            noDataDescription: n("noTypeFound"),
            hidden: !h?.[yU.SUB_CATEGORY]?.[0]?.id,
            rightRadioInput: !1
        }]?.filter(e => !e.hidden), M = [h?.[yU.PAGE] && 1 != h?.[yU.PAGE], h?.[yU.SEARCH] && "" !== h?.[yU.SEARCH], g?.sortBy && "desc.created_at" !== g?.id, !!h?.[yU.STATUS]?.length, !!h?.[yU.COMMUNITY]?.length, !!h?.[yU.SUB_CATEGORY]?.length, !!h?.[yU.TYPE]?.length]?.some(Boolean);
        return {
            filters: C,
            search: f,
            page: m,
            sort: g,
            selectedFilters: h,
            clearForm: () => {
                x({
                    [yU.CATEGORY]: e,
                    [yU.STATUS]: [],
                    [yU.COMMUNITY]: [],
                    [yU.SUB_CATEGORY]: [],
                    [yU.TYPE]: [],
                    [yU.IS_HISTORY]: t
                }), y(""), v(1), _({
                    sortBy: "",
                    sortDirection: "desc"
                })
            },
            isFilterApplied: M,
            setSearch: y,
            setPage: v
        }
    }, t4 = {
        selectField: {
            backgroundColor: "transparent",
            width: "fit-content",
            padding: "11px 16px"
        }
    };

function n4({
    categoryId: e,
    isHistory: t,
    userID: n,
    leaseId: r
}) {
    const {
        selectedFilters: a,
        search: i,
        page: o,
        sort: s
    } = e4({
        categoryId: e,
        isHistory: t
    }), {
        data: l,
        isLoading: d
    } = tl({
        queryKey: [hF, n, t, e, r, i, o, s, ...Object.values(a)],
        queryFn: () => (async e => {
            const t = await lo("/api-management/rf/users/requests" + (e.rf_category_id === fU.neighbourhoodServices ? "/common-area" : ""), {
                is_paginate: 1,
                page: e.page || 1,
                query: e.query,
                rf_category_id: e.rf_category_id,
                sortDirection: e.sortDirection,
                sortBy: e.sortBy || null,
                rf_status_id: e.rf_status_id?.map(e => e?.id ?? e) || [],
                rf_community_id: e.rf_community_id?.map(e => e?.id ?? e) || [],
                rf_sub_category_id: e.rf_sub_category_id?.map(e => e?.id ?? e) || [],
                rf_type_id: e.rf_type_id?.map(e => e?.id ?? e) || [],
                request_history: e.request_history,
                user_id: e.user_id,
                rf_lease_id: e.rf_lease_id,
                limit: 30
            });
            return n = t.data, {
                list: n?.list?.map(e => ({
                    category: e.category?.id,
                    status: {
                        id: e.last_request_status?.id,
                        name: e.last_request_status?.name
                    },
                    unit: e.unit?.name,
                    building: e.unit?.rf_building?.name,
                    community: e?.rf_community?.name,
                    createdAt: e.created_at,
                    icon: e.subCategory?.icon?.url,
                    type: e.type?.name,
                    subCategory: {
                        id: e.subCategory?.id,
                        name: e.subCategory?.name
                    },
                    id: e.id,
                    scheduleTime: e.date_time,
                    startDate: e.start_date
                })),
                meta: {
                    page: n.paginator?.current_page,
                    count: n.paginator?.last_page,
                    total: n.paginator?.total
                }
            };
            var n
        })({
            ...a,
            sortBy: s?.sortBy,
            sortDirection: s?.sortBy ? s?.sortDirection : void 0,
            query: i,
            page: o,
            rf_category_id: e,
            user_id: n,
            rf_lease_id: r
        })
    }), c = l?.list;
    return {
        isLoading: d,
        isEmpty: c && !c.length,
        count: l?.meta?.count,
        total: l?.meta?.total,
        requestsList: c
    }
}

function r4({
    isHistory: t,
    showHeader: n = !0,
    userID: r,
    categoryId: a,
    leaseId: i
}) {
    const [o] = $t(), s = o.get("type"), {
        requestsList: l,
        isEmpty: d,
        isLoading: c,
        count: u,
        total: p
    } = n4({
        categoryId: a || +s,
        isHistory: t,
        userID: r,
        leaseId: i
    }), {
        filters: h,
        clearForm: m,
        isFilterApplied: f,
        search: g,
        setSearch: y,
        page: v,
        setPage: _,
        selectedFilters: x
    } = e4({
        categoryId: a || +s,
        isHistory: t
    });
    return e.jsxs(sP, {
        children: [e.jsx(q3, {
            total: p,
            type: +s,
            isHistory: t,
            showHeader: n
        }), e.jsx(z3, {
            requestsList: l,
            isEmpty: d,
            isLoading: c,
            count: u,
            filters: h,
            clearForm: m,
            isFilterApplied: f,
            search: g,
            setSearch: y,
            page: v,
            setPage: _,
            selectedFilters: x
        })]
    })
}
const a4 = Object.freeze(Object.defineProperty({
    __proto__: null,
    default: r4
}, Symbol.toStringTag, {
    value: "Module"
}));

function i4({
    isHistory: t = !1,
    isLease: n = !1
}) {
    const {
        t: r
    } = Gn(), {
        id: a
    } = qt(), [i, o] = Dt.useState(1);
    return e.jsxs(ap, {
        maxWidth: "xl",
        pt: "16px",
        children: [e.jsx(IQ, {}), e.jsxs(ap, {
            component: "header",
            xbetween: !0,
            my: "16px",
            alignItems: "center",
            children: [e.jsx(hp, {
                variant: "h4",
                children: r("breadcrumb.serviceRequests")
            }), !t && e.jsx(U3, {
                path: "history"
            })]
        }), e.jsx(r4, {
            leaseId: n ? +a : void 0,
            showHeader: !1,
            userID: n ? void 0 : a,
            categoryId: 1,
            isHistory: t ? 1 : 0
        })]
    })
}
const o4 = Object.freeze(Object.defineProperty({
        __proto__: null,
        default: i4
    }, Symbol.toStringTag, {
        value: "Module"
    })),
    s4 = Dt.lazy(() => SZ(() => rr(() => import("./BookingsList-B2SQw4Ql.js"), __vite__mapDeps([95, 1, 2, 3, 96, 6])))),
    l4 = Dt.lazy(() => SZ(() => rr(() => import("./BookingDetails-CbFFEv1p.js"), __vite__mapDeps([97, 1, 2, 3, 96, 6])))),
    d4 = [{
        path: "bookings",
        title: "bookings",
        element: e.jsx(Zt, {}),
        children: [{
            path: "",
            title: "bookings",
            element: e.jsx(s4, {})
        }, {
            path: "history",
            title: "bookingsHistory",
            element: e.jsx(s4, {
                isHistory: !0
            })
        }, {
            path: ":id",
            title: "bookingDetails",
            element: e.jsx(l4, {})
        }]
    }];

function c4(e) {
    const t = "EXPIRING_LEASES" + e,
        [n, r] = Dt.useReducer(sJ, oJ),
        {
            page: a
        } = n,
        {
            data: i,
            error: o,
            isError: s
        } = tl([t, e, a], async () => await (async ({
            type: e,
            page: t
        }) => await lo(`/api-management/dashboard/require-attentions/expiringLeases?type=${e}&page=${t}`))({
            type: e,
            page: a
        }));
    return s && o && Lo(o, {
        setError: o
    }, !0), {
        data: i?.data,
        total: i?.meta?.total,
        count: i?.meta?.last_page,
        dispatch: r,
        page: a
    }
}
const u4 = ({
        children: t,
        title: n
    }) => e.jsxs(ft, {
        disableGutters: !0,
        elevation: 0,
        sx: {
            mb: 12,
            backgroundColor: "transparent",
            "&:before": {
                display: "none",
                bgcolor: "#f5f5f9"
            }
        },
        children: [e.jsx(gt, {
            expandIcon: e.jsx(yt, {
                sx: {
                    fontSize: 30
                }
            }),
            "aria-controls": "panel1a-content",
            id: "panel1a-header",
            children: e.jsx(rP, {
                variant: "h5",
                s: 18,
                sx: {
                    fontWeight: "700"
                },
                children: n
            })
        }), e.jsx(vt, {
            sx: {
                mt: "24px"
            },
            children: t
        })]
    }),
    p4 = ({
        title: t,
        color: n,
        backgroundColor: r
    }) => e.jsx(ve, {
        label: t,
        sx: {
            height: "24px",
            fontWeight: "500",
            backgroundColor: r,
            borderRadius: "4px",
            fontSize: "1.2rem",
            color: n,
            textTransform: "capitalize",
            "& .MuiChip-label": {
                paddingLeft: "8px",
                paddingRight: "8px"
            }
        },
        variant: "filled"
    });
var h4, m4 = {};

function f4() {
    if (h4) return m4;
    h4 = 1;
    var e = h();
    Object.defineProperty(m4, "__esModule", {
        value: !0
    }), m4.default = void 0;
    var t = e(jp()),
        n = m();
    return m4.default = (0, t.default)((0, n.jsx)("path", {
        d: "M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7m0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5"
    }), "LocationOn"), m4
}
const g4 = It(f4());
var y4, v4 = {};

function _4() {
    if (y4) return v4;
    y4 = 1;
    var e = h();
    Object.defineProperty(v4, "__esModule", {
        value: !0
    }), v4.default = void 0;
    var t = e(jp()),
        n = m();
    return v4.default = (0, t.default)((0, n.jsx)("path", {
        d: "M6.62 10.79c1.44 2.83 3.76 5.14 6.59 6.59l2.2-2.2c.27-.27.67-.36 1.02-.24 1.12.37 2.33.57 3.57.57.55 0 1 .45 1 1V20c0 .55-.45 1-1 1-9.39 0-17-7.61-17-17 0-.55.45-1 1-1h3.5c.55 0 1 .45 1 1 0 1.25.2 2.45.57 3.57.11.35.03.74-.25 1.02z"
    }), "LocalPhone"), v4
}
const x4 = It(_4()),
    b4 = ({
        handleClose: t,
        isOpen: n,
        phone: r
    }) => {
        const {
            t: a
        } = Gn();
        return e.jsxs(v, {
            onClose: t,
            open: n,
            maxWidth: "sm",
            children: [e.jsx(TJ, {
                title: a("dashboard.expiringLeases.callTenant"),
                handleClose: t
            }), e.jsx(_, {
                sx: {
                    textAlign: "center",
                    mx: 30,
                    my: 24
                },
                children: e.jsx(l, {
                    sx: {
                        display: "flex",
                        flexDirection: "column",
                        boxShadow: "none"
                    },
                    component: "a",
                    variant: "text",
                    href: `tel:${r}`,
                    startIcon: e.jsx(x4, {
                        sx: {
                            fontSize: "60px !important"
                        }
                    }),
                    children: e.jsx(o, {
                        variant: "h5",
                        sx: {
                            fontWeight: "500",
                            mt: 8
                        },
                        children: r
                    })
                })
            })]
        })
    },
    w4 = ({
        item: t
    }) => {
        const {
            t: n
        } = Gn(), r = Ft(), [a, i] = Dt.useState(!1);
        return e.jsxs(et, {
            sx: {
                minHeight: "100%",
                display: "grid"
            },
            children: [e.jsxs(sP, {
                justifyContent: "space-between",
                alignItems: "start",
                gap: 4,
                sx: {
                    mt: 6
                },
                children: [e.jsxs(lP, {
                    xs: 4,
                    sx: {
                        mb: 4
                    },
                    children: [e.jsx(o, {
                        variant: "caption",
                        sx: {
                            color: Ge[500],
                            fontWeight: "400"
                        },
                        children: n("requests.status")
                    }), e.jsx(o, {
                        children: tR(t?.lease?.[0]?.end_date || t?.lease?.end_date).diff(tR(), "days") > 0 ? e.jsx(p4, {
                            title: n("status.Active"),
                            color: "#008EA5",
                            backgroundColor: "rgba(31, 68, 139, 0.08)"
                        }) : e.jsx(p4, {
                            title: n("status.Expired"),
                            color: "#FF0000",
                            backgroundColor: "#FFEBEB"
                        })
                    })]
                }), e.jsxs(lP, {
                    xs: 3,
                    sx: {
                        mb: 4
                    },
                    children: [e.jsx(o, {
                        variant: "caption",
                        sx: {
                            color: Ge[500],
                            fontWeight: "400"
                        },
                        children: n("dashboard.expiringLeases.unit")
                    }), e.jsx(o, {
                        variant: "subtitle2",
                        sx: {
                            fontWeight: "500"
                        },
                        children: t?.unit?.name || t?.name
                    })]
                }), t?.building?.name ? e.jsxs(lP, {
                    xs: 4,
                    sx: {
                        mb: 4
                    },
                    children: [e.jsx(o, {
                        variant: "caption",
                        sx: {
                            color: Ge[500],
                            fontWeight: "400"
                        },
                        children: n("dashboard.expiringLeases.building")
                    }), e.jsx(o, {
                        variant: "subtitle2",
                        sx: {
                            fontWeight: "500"
                        },
                        children: t?.building?.name
                    })]
                }) : null, t?.community?.name ? e.jsxs(lP, {
                    xs: 4,
                    sx: {
                        mb: 4
                    },
                    children: [e.jsx(o, {
                        variant: "caption",
                        sx: {
                            color: Ge[500],
                            fontWeight: "400"
                        },
                        children: n("dashboard.expiringLeases.community")
                    }), e.jsx(o, {
                        variant: "subtitle2",
                        sx: {
                            fontWeight: "500"
                        },
                        children: t?.community?.name
                    })]
                }) : null, t?.district?.name && t?.city?.name ? e.jsxs(lP, {
                    sx: {
                        mb: 4
                    },
                    children: [e.jsx(o, {
                        variant: "caption",
                        sx: {
                            color: Ge[500],
                            fontWeight: "400"
                        },
                        children: n("dashboard.expiringLeases.location")
                    }), e.jsxs(o, {
                        variant: "subtitle2",
                        sx: {
                            fontWeight: "500",
                            display: "flex",
                            alignItems: "center"
                        },
                        children: [e.jsx(g4, {
                            sx: {
                                color: e => e?.palette?.primary?.main,
                                mr: 2
                            }
                        }), t?.district?.name, ", ", t?.city?.name]
                    })]
                }) : null, e.jsxs(lP, {
                    sx: {
                        mb: 4
                    },
                    children: [e.jsx(o, {
                        variant: "caption",
                        sx: {
                            color: Ge[500],
                            fontWeight: "400"
                        },
                        children: n("dashboard.expiringLeases.leaseStartDate")
                    }), e.jsx(o, {
                        variant: "subtitle2",
                        sx: {
                            fontWeight: "500"
                        },
                        children: tR(t?.lease?.[0]?.start_date || t?.lease?.start_date).format("YYYY-MM-DD")
                    })]
                }), e.jsxs(lP, {
                    sx: {
                        mb: 4
                    },
                    children: [e.jsx(o, {
                        variant: "caption",
                        sx: {
                            color: Ge[500],
                            fontWeight: "400"
                        },
                        children: n("dashboard.expiringLeases.leaseEndDate")
                    }), e.jsx(o, {
                        variant: "subtitle2",
                        sx: {
                            fontWeight: "500"
                        },
                        children: tR(t?.lease?.[0]?.end_date || t?.lease?.end_date).format("YYYY-MM-DD")
                    })]
                }), tR(t?.lease?.[0]?.end_date || t?.lease?.end_date).diff(tR(), "days") > 0 ? e.jsxs(lP, {
                    sx: {
                        mb: 4
                    },
                    children: [e.jsx(o, {
                        variant: "caption",
                        sx: {
                            color: Ge[500],
                            fontWeight: "400"
                        },
                        children: n("dashboard.expiringLeases.daysRemaining")
                    }), e.jsx(o, {
                        variant: "subtitle2",
                        sx: {
                            fontWeight: "500",
                            color: _t[500]
                        },
                        children: tR(t?.lease?.[0]?.end_date || t?.lease?.end_date).diff(tR(), "days")
                    })]
                }) : null]
            }), e.jsxs(sP, {
                spacing: 4,
                sx: {
                    mt: 2
                },
                children: [e.jsx(lP, {
                    xs: 12,
                    sm: 6,
                    children: e.jsx(l, {
                        onClick: () => {
                            r(`/expiring-leases/${t?.id}`, {
                                state: {
                                    item: t
                                }
                            })
                        },
                        fullWidth: !0,
                        variant: "outlined",
                        color: "primary",
                        children: n("dashboard.expiringLeases.viewDetails")
                    })
                }), e.jsx(lP, {
                    xs: 12,
                    sm: 6,
                    children: e.jsx(l, {
                        fullWidth: !0,
                        variant: "contained",
                        color: "primary",
                        onClick: () => i(!0),
                        children: n("dashboard.expiringLeases.contactTenant")
                    })
                })]
            }), e.jsx(b4, {
                handleClose: () => i(!1),
                isOpen: a,
                phone: t?.tenant?.full_phone_number
            })]
        }, t.id)
    },
    C4 = ({
        data: t,
        title: n,
        footer: r
    }) => {
        const {
            t: a
        } = Gn();
        return e.jsx(zQ, {
            data: t,
            SectionWrapperComponent: UQ,
            renderItem: ({
                item: t
            }) => e.jsx(w4, {
                item: t
            }),
            Header: n,
            Footer: r
        })
    };

function M4() {
    const {
        t: t
    } = Gn(), {
        data: n,
        count: r,
        dispatch: a,
        page: i
    } = c4(1), {
        data: o,
        count: s,
        dispatch: l,
        page: d
    } = c4(2), {
        data: c,
        count: u,
        dispatch: p,
        page: h
    } = c4(3), {
        data: m,
        count: f,
        dispatch: g,
        page: y
    } = c4(4);
    return e.jsxs(e.Fragment, {
        children: [e.jsx(IQ, {}), e.jsxs(cP, {
            sx: {
                mt: "32px"
            },
            children: [e.jsx(u4, {
                title: t("dashboard.Days30"),
                children: e.jsx(C4, {
                    data: n,
                    title: e.jsx(e.Fragment, {}),
                    footer: e.jsx(HQ, {
                        page: i,
                        count: r,
                        handler: e => a({
                            type: "PAGE",
                            payload: e
                        })
                    })
                })
            }), e.jsx(u4, {
                title: t("dashboard.Days60"),
                children: e.jsx(C4, {
                    data: o,
                    title: e.jsx(e.Fragment, {}),
                    footer: e.jsx(HQ, {
                        page: d,
                        count: s,
                        handler: e => l({
                            type: "PAGE",
                            payload: e
                        })
                    })
                })
            }), e.jsx(u4, {
                title: t("dashboard.Days60+"),
                children: e.jsx(C4, {
                    data: c,
                    title: e.jsx(e.Fragment, {}),
                    footer: e.jsx(HQ, {
                        page: h,
                        count: u,
                        handler: e => p({
                            type: "PAGE",
                            payload: e
                        })
                    })
                })
            }), e.jsx(u4, {
                title: t("dashboard.Days90+"),
                children: e.jsx(C4, {
                    data: m,
                    title: e.jsx(e.Fragment, {}),
                    footer: e.jsx(HQ, {
                        page: y,
                        count: f,
                        handler: e => g({
                            type: "PAGE",
                            payload: e
                        })
                    })
                })
            })]
        })]
    })
}
const S4 = Object.freeze(Object.defineProperty({
    __proto__: null,
    default: M4
}, Symbol.toStringTag, {
    value: "Module"
}));
var L4, k4 = {};

function T4() {
    if (L4) return k4;
    L4 = 1;
    var e = h();
    Object.defineProperty(k4, "__esModule", {
        value: !0
    }), k4.default = void 0;
    var t = e(jp()),
        n = m();
    return k4.default = (0, t.default)((0, n.jsx)("path", {
        d: "M19.35 10.04C18.67 6.59 15.64 4 12 4 9.11 4 6.6 5.64 5.35 8.04 2.34 8.36 0 10.91 0 14c0 3.31 2.69 6 6 6h13c2.76 0 5-2.24 5-5 0-2.64-2.05-4.78-4.65-4.96M14 13v4h-4v-4H7l5-5 5 5z"
    }), "CloudUpload"), k4
}
const j4 = It(T4());

function E4(t) {
    const {
        showThumbs: n = !0,
        name: r,
        label: a = "",
        filesLimit: i = 0,
        acceptedFiles: o = [],
        form: s,
        dropzoneText: d,
        errors: c,
        onImageSelect: u,
        onDelete: p,
        maxFileSize: h,
        customErrors: m
    } = t, [f, g] = Dt.useState([]), {
        setValue: y,
        clearErrors: v,
        register: _
    } = s, x = s.getValues(), {
        t: b
    } = Gn();
    Dt.useEffect(() => {
        (x?.file || t?.files) && g((x.file || [...t.files]).map(e => (Object.assign(e, {
            edit: !0
        }), e)))
    }, [x.file, t?.files]);
    const {
        getRootProps: w,
        getInputProps: C
    } = bD({
        accept: o?.length > 0 ? o : void 0,
        onDrop: e => {
            const t = (1 === i ? e : [...x.file || [], ...e]).map(e => Object.assign(e, {
                preview: URL.createObjectURL(e)
            }));
            M(t) && (u?.(t), L(t), g(t))
        }
    }), M = e => {
        if (e?.length > i) return Zi.error(m && (m.count ?? `User can only upload ${i} files.`)), !1;
        if (S(e)) return Zi.error(m && (m.size ?? `File size should be less than ${t.maxFileSize/1e3} MB`)), !1;
        for (const t of e)
            if (!o.includes(t?.type)) return Zi.error(m && (m.format ?? b("error.fileformat"))), !1;
        return !0
    }, S = e => {
        for (const t of e)
            if (t.size > h) return !0
    }, L = e => {
        0 === e.length ? y(r, "", {
            shouldDirty: !0
        }) : (y(r, e, {
            shouldDirty: !0
        }), v([r]))
    }, k = (t = "new") => (n && f || []).filter(e => "new" === t ? !e.edit : e.edit).map((t, n) => e.jsxs(ap, {
        sx: D4.imageContainer,
        style: t?.type?.includes("image") ? {} : {
            display: "flex"
        },
        children: [e.jsx("div", {
            style: {
                height: 90
            },
            children: t?.type?.includes("image") ? e.jsx(ap, {
                component: "img",
                src: t.preview,
                height: "90px",
                sx: D4.thumb,
                alt: ""
            }) : e.jsx(ap, {
                component: "img",
                src: t?.url,
                height: "90px",
                sx: D4.thumb,
                alt: ""
            })
        }), e.jsx(l, {
            sx: {
                maxHeight: "45px",
                ...t?.type?.includes("image") ? D4.deleteIcon : {}
            },
            onClick: e => ((e, t) => {
                e.stopPropagation();
                let n = [...x.file || []];
                p?.(n[t]), n.splice(t, 1), y(r, n, {
                    shouldDirty: !0
                }), g(n)
            })(e, n),
            children: e.jsx(th, {
                sx: {
                    color: "red"
                }
            })
        })]
    }, t.name));
    return e.jsxs(e.Fragment, {
        children: [e.jsx(rP, {
            light: !0,
            s: 14,
            sx: {
                mb: "8px",
                color: "#525451"
            },
            children: a
        }), e.jsxs("div", {
            ...w({
                className: "dropzone"
            }),
            children: [e.jsx("input", {
                ..._("file"),
                ...C()
            }), t.dropZoneAreaComponent?.() ?? e.jsxs(ap, {
                sx: D4.fileTextContainer,
                children: [e.jsx(j4, {}), e.jsx(rP, {
                    s: 20,
                    color: "Greyscale900",
                    children: d || b("signUp.uploadFiles")
                }), e.jsx(rP, {
                    s: 14,
                    color: "Greyscale500",
                    sx: {
                        fontWeight: 400,
                        mt: 4
                    },
                    children: e.jsx(Xn, {
                        i18nKey: "common.DNDFiles",
                        components: [e.jsx(rP, {
                            s: 14,
                            component: "span"
                        })]
                    })
                })]
            })]
        }), f?.filter(e => !e.edit)?.length ? e.jsx(ap, {
            sx: D4.editThumbsContainer,
            children: k()
        }) : [], f?.filter(e => e.edit)?.length ? e.jsx(ap, {
            sx: D4.editThumbsContainer,
            children: k("edit")
        }) : [], c.hasOwnProperty(r) && !c.hasOwnProperty("exception") ? e.jsx(rP, {
            s: 14,
            sx: {
                color: "red",
                my: 6,
                fontWeight: 400
            },
            children: b("common.thisRequired")
        }) : ""]
    })
}
const D4 = {
        fileTextContainer: {
            display: "flex",
            justifyContent: "center",
            alignItems: "center",
            flexDirection: "column",
            outlineStyle: "dashed",
            outlineColor: "rgba(213, 213, 213, 1)",
            outlineWidth: "2px",
            borderRadius: "10px",
            height: "143px",
            width: "99%",
            position: "relative",
            background: "white"
        },
        editThumbsContainer: {
            display: "flex",
            border: "1px solid rgba(213, 213, 213, 1)",
            padding: "10px",
            flexWrap: "wrap"
        },
        imageContainer: {
            padding: "8px",
            position: "relative",
            maxHeight: 90
        },
        thumb: {
            objectFit: "contain"
        },
        deleteIcon: {
            position: "absolute",
            top: 10,
            right: 10,
            cursor: "pointer"
        }
    },
    V4 = "data:image/svg+xml,%3csvg%20width='79'%20height='66'%20viewBox='0%200%2079%2066'%20fill='none'%20xmlns='http://www.w3.org/2000/svg'%3e%3cpath%20d='M53.1204%2046.4157L39.7594%2033.0547L26.3984%2046.4157'%20stroke='%23CECECE'%20stroke-width='5'%20stroke-linecap='round'%20stroke-linejoin='round'/%3e%3cpath%20d='M39.7617%2033.0547V63.1167'%20stroke='%23008EA5'%20stroke-width='5'%20stroke-linecap='round'%20stroke-linejoin='round'/%3e%3cpath%20d='M67.7846%2054.3953C71.0435%2052.6199%2073.6182%2049.8096%2075.1021%2046.408C76.5861%2043.0065%2076.8947%2039.2076%2075.9794%2035.6111C75.064%2032.0147%2072.9767%2028.8255%2070.0472%2026.5473C67.1176%2024.2691%2063.5127%2023.0317%2059.8016%2023.0303H55.5926C54.5811%2019.1199%2052.6964%2015.4898%2050.0801%2012.4126C47.4638%209.33552%2044.184%206.89152%2040.4872%205.26436C36.7904%203.6372%2032.7729%202.86923%2028.7366%203.01818C24.7003%203.16712%2020.7503%204.2291%2017.1835%206.1243C13.6167%208.01949%2010.526%2010.6986%208.14355%2013.9602C5.76115%2017.2217%204.14911%2020.9809%203.4286%2024.9552C2.7081%2028.9294%202.89789%2033.0153%203.98369%2036.9056C5.0695%2040.796%207.02308%2044.3896%209.69756%2047.4163'%20stroke='%23CECECE'%20stroke-width='5'%20stroke-linecap='round'%20stroke-linejoin='round'/%3e%3cpath%20d='M53.1204%2046.4157L39.7594%2033.0547L26.3984%2046.4157'%20stroke='%23008EA5'%20stroke-width='5'%20stroke-linecap='round'%20stroke-linejoin='round'/%3e%3c/svg%3e",
    A4 = ({
        status: t,
        handleTryAgain: n,
        excelErrors: r,
        apiErrorMessage: a
    }) => {
        const {
            t: i
        } = Gn(), o = Ft(), s = Ys(), l = () => {
            n()
        };
        return "failed" === t ? e.jsx(lh, {
            isOpen: "failed" === t,
            variant: "error",
            content: {
                title: i("properties.bulkUpload_failed"),
                body: a || i("properties.failure_message")
            },
            closeBtnText: i("properties.tryagain"),
            primaryButton: r?.length ? {
                title: i("view issues"),
                handleClick: () => {
                    o("bulk-upload-errors", {
                        state: {
                            excelErrors: r
                        }
                    })
                },
                variant: "contained"
            } : null,
            onDialogClose: l
        }) : "success" === t ? e.jsx(lh, {
            isOpen: "success" === t,
            variant: "success",
            content: {
                title: i("properties.bulkUpload_success"),
                body: i("properties.bulkUpload_success_body")
            },
            closeBtnText: i("uploadMoreFiles"),