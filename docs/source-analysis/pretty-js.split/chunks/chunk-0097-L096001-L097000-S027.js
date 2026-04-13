                title: t?.name,
                body: `${+t?.value} ${n("properties.add_unit.sqm")}`
            })) : null,
            additionalRooms: t?.rooms?.length > 0 ? t?.rooms?.map(t => ({
                icon: e.jsx(s8, {}),
                title: t?.name,
                body: +t?.value
            })) : null
        }))(t, n);
        return e.jsxs(vt, {
            sx: {
                p: "0 16px"
            },
            children: [e.jsx(Le, {
                sx: {
                    display: "grid",
                    gridTemplateColumns: "repeat(5, 1fr)",
                    rowGap: "24px",
                    py: "24px"
                },
                children: r?.general?.filter(e => null != e && "title" in e)?.map(e => Dt.createElement(L8, {
                    ...e,
                    key: e?.title
                }))
            }), r?.additionalRooms && e.jsxs(Le, {
                borderTop: "1px solid #E3E3E3",
                py: "24px",
                children: [e.jsx(rP, {
                    s: "16",
                    mb: "16px",
                    children: n("unitForm.additionalRooms")
                }), e.jsx(Le, {
                    sx: {
                        display: "grid",
                        gridTemplateColumns: "repeat(5, 1fr)",
                        rowGap: "24px"
                    },
                    children: r?.additionalRooms?.filter(e => null != e && "title" in e)?.map(t => t.title ? Dt.createElement(L8, {
                        ...t,
                        key: t?.title
                    }) : e.jsx(rP, {
                        s: "14",
                        light: !0,
                        gray: !0,
                        children: n("leasing.noVal")
                    }, t?.title))
                })]
            }), r?.areaBreakdown && e.jsxs(Le, {
                borderTop: "1px solid #E3E3E3",
                py: "24px",
                children: [e.jsx(rP, {
                    s: "16",
                    mb: "16px",
                    children: n("unitForm.additionalUnitAreas")
                }), e.jsx(Le, {
                    sx: {
                        display: "grid",
                        gridTemplateColumns: "repeat(5, 1fr)",
                        rowGap: "24px"
                    },
                    children: r?.areaBreakdown?.filter(e => null != e && "title" in e)?.map(t => t.title ? Dt.createElement(L8, {
                        ...t,
                        key: t?.title
                    }) : e.jsx(rP, {
                        s: "14",
                        light: !0,
                        gray: !0,
                        children: n("leasing.noVal")
                    }, t?.title))
                })]
            })]
        })
    },
    z8 = t => {
        const n = s(),
            r = t.color ?? n.palette.primary.primaryDark;
        return e.jsx(i, {
            ...t,
            inheritViewBox: !0,
            children: e.jsxs("svg", {
                viewBox: "0 0 24 24",
                fill: "none",
                xmlns: "http://www.w3.org/2000/svg",
                children: [e.jsx("circle", {
                    cx: "12",
                    cy: "12",
                    r: t?.width ?? "10",
                    fill: r
                }), e.jsx("path", {
                    "fill-rule": "evenodd",
                    "clip-rule": "evenodd",
                    d: "M12 17C11.4477 17 11 16.5523 11 16L11 11.5C11 10.9477 11.4477 10.5 12 10.5C12.5523 10.5 13 10.9477 13 11.5L13 16C13 16.5523 12.5523 17 12 17Z",
                    fill: "white"
                }), e.jsx("path", {
                    "fill-rule": "evenodd",
                    "clip-rule": "evenodd",
                    d: "M12 9.5C11.4477 9.5 11 9.05228 11 8.5L11 8C11 7.44772 11.4477 7 12 7C12.5523 7 13 7.44772 13 8L13 8.5C13 9.05228 12.5523 9.5 12 9.5Z",
                    fill: "white"
                })]
            })
        })
    };

function U8() {
    const {
        t: t,
        i18n: {
            language: n
        }
    } = Gn(), r = s(), a = ce(r.breakpoints.up("xl")), {
        lease: i,
        isLoading: o,
        isRemovingLease: d,
        leaseActionStrategy: c,
        onDelete: u,
        goBack: p,
        showDeleteDialogue: h,
        setShowDeleteDialogue: m,
        showDeleteConfirmation: f
    } = Q5(), {
        subleases: g,
        addNewSublease: y,
        removeAllSubleases: v,
        saveSublease: _,
        removeSublease: x,
        editSublease: b,
        isRemovingSublease: w,
        mutateLoading: C,
        shouldShowRemoveBtn: M
    } = D8(i?.subleases);
    if (o) return e.jsx(hP, {});
    if (!i) return;
    const S = M8(i, t);
    return e.jsxs(sP, {
        maxWidth: "xl",
        gap: "26px",
        column: !0,
        children: [e.jsx(X5, {
            status: {
                id: i?.status,
                name: i?.statusName
            },
            actions: c?.[i?.status]
        }), i.isOld && e.jsxs(cP, {
            row: !0,
            sx: {
                backgroundColor: "#FCEDC7",
                alignItems: "center",
                my: "24px",
                p: "12px",
                gap: "12px",
                borderRadius: "8px"
            },
            children: [e.jsx(z8, {
                color: "#FFC225"
            }), e.jsx(rP, {
                s: 16,
                light: !0,
                children: t("leasing.oldNote")
            })]
        }), e.jsxs(cP, {
            gap: "24px",
            column: !0,
            children: [e.jsxs(e8, {
                title: t("contacts.Tenant Details"),
                cols: a ? 7 : 6,
                children: [i?.tenant?.name || i?.tenant?.name_en || i?.tenant?.name_ar ? e.jsx(k8, {
                    tenant: i?.tenant,
                    sx: {
                        mr: "24px"
                    },
                    isReview: !0
                }) : null, S?.tenant?.filter(e => null != e)?.map(e => Dt.createElement(L8, {
                    ...e,
                    key: e.title
                })), e.jsx("br", {}), S?.companyRepresentative && e.jsxs(e8, {
                    title: t("leasing.companyRepresentative"),
                    cols: a ? 7 : 5,
                    sx: {
                        padding: "16px",
                        gridColumn: "span 7",
                        "& .MuiTypography-root": {
                            fontSize: "16px !important",
                            marginBottom: "24px"
                        }
                    },
                    children: [S?.companyRepresentative?.filter(e => null != e)?.map(e => Dt.createElement(L8, {
                        ...e,
                        key: e.title
                    })), e.jsx(cP, {
                        row: !0,
                        width: "100%",
                        gridColumn: "span 7",
                        children: i?.tenant?.representative?.documents?.map(t => e.jsx(P8, {
                            file: t,
                            removeImage: () => {},
                            isDeleting: !0,
                            onFileClick: () => window.open(t.url, "__blank")
                        }, t.id))
                    })]
                })]
            }), e.jsx(e8, {
                title: t("breadcrumb.unit-details"),
                cols: 1,
                sx: {
                    "& .MuiPaper-root": {
                        borderRadius: "8px !important",
                        overflow: "hidden",
                        border: "1px solid #E3E3E3",
                        boxShadow: "none"
                    }
                },
                children: i?.units?.map(n => e.jsx(Y8, {
                    content: n,
                    data: [{
                        icon: e.jsx(i8, {}),
                        title: t("leasing.netArea_preview"),
                        body: n?.area ? `${n?.area} ${t("properties.add_unit.sqm")}` : t("leasing.noVal")
                    }, {
                        icon: e.jsx(i8, {}),
                        title: t("unitForm.marketRent"),
                        body: n?.MarketRent ? `${n?.MarketRent} ${t("SAR")}` : t("leasing.noVal")
                    }],
                    children: e.jsx(B8, {
                        content: n
                    })
                }, n.id))
            }), e.jsx(e8, {
                title: t("breadcrumb.leaseDetails"),
                cols: 5,
                children: S?.details?.filter(e => null != e)?.map(e => Dt.createElement(L8, {
                    ...e,
                    key: e?.title
                }))
            }), i?.escalation && !!i?.escalation?.length && i?.contract?.rentalTypeValue !== Z4.MONTHLY && i?.contract?.rentalTypeValue !== Z4.DAILY && e.jsx(e8, {
                title: t("leasing.escalation"),
                cols: 1,
                children: e.jsx(j8, {
                    headers: S?.escalation?.headers,
                    rows: S?.escalation?.rows
                })
            }), i?.payment && !!i?.payment?.length && e.jsx(e8, {
                title: t("breadcrumb.Transactions-details"),
                cols: 1,
                children: e.jsx(j8, {
                    headers: S?.payment?.headers,
                    rows: S?.payment?.rows
                })
            }), i?.deposit && e.jsx(e8, {
                title: t("lease.deposit_fee"),
                cols: 5,
                children: S?.deposit?.filter(e => null != e)?.map(e => Dt.createElement(L8, {
                    ...e,
                    key: e?.title
                }))
            }), i?.tsAndCs && e.jsx(e8, {
                title: t("leasing.tsTitle"),
                cols: 1,
                children: e.jsx(rP, {
                    s: 16,
                    light: !0,
                    children: S?.tsAndCs
                })
            }), e.jsx(T8, {
                title: t("leasing.sublease"),
                subtitle: t("leasing.subleaseSubtitle"),
                cols: 1,
                actionBtn: 0 === g?.length ? e.jsx(l, {
                    startIcon: e.jsx(jf, {}),
                    onClick: y,
                    variant: "text",
                    color: "primary",
                    children: t("common.add")
                }) : M ? e.jsx(l, {
                    onClick: v,
                    variant: "text",
                    color: "error",
                    children: t("common.delete")
                }) : null,
                children: g?.length > 0 && e.jsxs(e.Fragment, {
                    children: [g?.map(t => e.jsx(R8, {
                        sublease: t,
                        saveSublease: _,
                        editSublease: b,
                        removeSublease: x,
                        mutateLoadingState: {
                            isRemovingSublease: w,
                            mutateLoading: C
                        }
                    }, t.id)), e.jsx(l, {
                        onClick: y,
                        startIcon: e.jsx(jf, {}),
                        sx: {
                            justifyContent: "flex-start",
                            width: "fit-content"
                        },
                        children: t("requestsCategories.Add More")
                    })]
                })
            })]
        }), e.jsx(QW, {
            icon: !1,
            content: {
                title: t("leasing.deleteLease") + " !",
                body: t("leasing.confirmDelete"),
                errors: []
            },
            isOpen: h,
            primaryButton: {
                handleClick: u,
                disabled: d,
                title: t("common.yes")
            },
            renderCloseBtn: () => e.jsx(l, {
                variant: "text",
                sx: {
                    color: "#232425"
                },
                onClick: () => m(!1),
                children: t("common.no")
            })
        }), e.jsx(QW, {
            content: {
                title: t("leasing.deleteSuccessTitle") + "!",
                body: t("leasing.deleteSuccessSubtitle")
            },
            isOpen: f,
            renderCloseBtn: () => e.jsx(l, {
                variant: "text",
                sx: {
                    color: e => e?.palette?.success?.main
                },
                onClick: p,
                children: t("common.ok")
            })
        })]
    })
}
const W8 = [{
        sortBy: "contract_number",
        value: "asc",
        label: "leasing.sortNum_asc"
    }, {
        sortBy: "contract_number",
        value: "desc",
        label: "leasing.sortNum_desc"
    }, {
        sortBy: "created_at",
        value: "asc",
        label: "leasing.date_asc"
    }, {
        sortBy: "created_at",
        value: "desc",
        label: "leasing.date_desc"
    }],
    Z8 = {
        "requests.status": [{
            label: "requests.new",
            id: 30,
            name: "status"
        }, {
            label: "leasing.active",
            id: 31,
            name: "status"
        }, {
            label: "leasing.terminated",
            id: 33,
            name: "status"
        }, {
            label: "status.Expired",
            id: 32,
            name: "status"
        }],
        "leasing.daysRemaining": [{
            label: "leasing.days.30",
            id: "1",
            name: "days"
        }, {
            label: "leasing.days.60",
            id: "2",
            name: "days"
        }, {
            label: "leasing.days.90",
            id: "3",
            name: "days"
        }, {
            label: "leasing.days.91",
            id: "4",
            name: "days"
        }]
    },
    q8 = {
        "requests.status": [{
            label: "requests.new",
            id: 30,
            name: "status"
        }, {
            label: "leasing.active",
            id: 31,
            name: "status"
        }],
        "leasing.daysRemaining": [{
            label: "leasing.days.30",
            id: 1,
            name: "days"
        }, {
            label: "leasing.days.60",
            id: 2,
            name: "days"
        }, {
            label: "leasing.days.90",
            id: 3,
            name: "days"
        }, {
            label: "leasing.days.91",
            id: 4,
            name: "days"
        }]
    },
    $8 = {
        "requests.status": [{
            label: "leasing.terminated",
            id: 33,
            name: "status"
        }, {
            label: "status.Expired",
            id: 32,
            name: "status"
        }]
    },
    G8 = [{
        sortBy: "contract_number",
        value: "asc",
        label: "leasing.sortNum_asc"
    }, {
        sortBy: "contract_number",
        value: "desc",
        label: "leasing.sortNum_desc"
    }, {
        sortBy: "created_at",
        value: "asc",
        label: "leasing.date_asc"
    }, {
        sortBy: "created_at",
        value: "desc",
        label: "leasing.date_desc"
    }, {
        sortBy: "updated_at",
        value: "desc",
        label: "leasing.last_modified"
    }],
    K8 = {
        "requests.status": [{
            label: "paid",
            id: 1,
            name: "status"
        }, {
            label: "overdue",
            id: 3,
            name: "status"
        }, {
            label: "outstanding",
            id: 2,
            name: "status"
        }]
    },
    Q8 = async e => {
        try {
            const n = await lo("/api-management/rf/leases/statistics", {
                community_id: e
            });
            return t = n, {
                metrics: {
                    total: {
                        number: t?.data?.totalLeases,
                        percentage: null
                    },
                    new: {
                        number: t?.data?.newLeases,
                        percentage: t?.data?.percentNewLeases
                    },
                    active: {
                        number: t?.data?.activeLeases,
                        percentage: t?.data?.percentActiveLeases
                    },
                    expired: {
                        number: t?.data?.expiredLeases,
                        percentage: t?.data?.percentExpiredLeases
                    },
                    terminated: {
                        number: t?.data?.terminatedLeases,
                        percentage: t?.data?.percentTerminatedLeases
                    }
                },
                charts: {
                    leasesStats: {
                        commercial: t?.data?.activeCommercialLeases,
                        residential: t?.data?.activeResidentialLeases
                    },
                    monthCollection: {
                        total: t?.data?.currentMonthCollection,
                        collected: t?.data?.calculatePaidCollectionForCurrentMonth
                    },
                    yearCollection: {
                        total: t?.data?.currentYearCollection,
                        collected: t?.data?.calculatePaidCollectionForCurrentYear
                    }
                }
            }
        } catch (n) {
            throw n
        }
        var t
    }, J8 = async ({
        community: e,
        search: t,
        filter: n,
        sort: r,
        page: a
    }) => {
        const i = {
            community: [e],
            query: t,
            status: n?.status ? n.status?.filter(e => [K5.NEW, K5.ACTIVE].includes(+e)) : [K5.NEW, K5.ACTIVE],
            sortBy: r.sortBy || "created_at",
            sortDirection: r.sortDirection || "desc",
            page: a,
            is_paginate: 1,
            limit: 50,
            days_remaining: n?.days
        };
        Object.keys(i).forEach(e => {
            void 0 !== i[e] && null !== i[e] && "" !== i[e] || delete i[e]
        });
        try {
            const e = await lo("/api-management/rf/leases", i);
            return y$(e)
        } catch (o) {
            throw o
        }
    };

function X8() {
    const [e, t] = Dt.useState(""), [n, r] = Dt.useState({}), [a, i] = Dt.useState({
        sortBy: "",
        sortDirection: ""
    }), [o, s] = Dt.useState(1), {
        t: l
    } = Gn(), {
        data: d,
        isLoading: c,
        isError: u
    } = tl([CH, e, n, a, o], async () => await (async ({
        search: e,
        filter: t,
        sort: n,
        page: r
    }) => {
        try {
            const a = t.status ? t.status?.filter(e => [K5.TERMINATED, K5.EXPIRED].includes(e)) : [K5.TERMINATED, K5.EXPIRED],
                i = await lo("/api-management/rf/leases", {
                    query: e,
                    status: a,
                    sortBy: n.sortBy || "updated_at",
                    sortDirection: n.sortDirection || "desc",
                    page: r,
                    is_paginate: 1,
                    limit: 50,
                    days_remaining: t.days
                });
            return y$(i)
        } catch (a) {
            throw a
        }
    })({
        search: e,
        filter: n,
        sort: a,
        page: o
    }));
    u && Zi.error(l("leasing.leasesListFailure"), {
        toastId: "fetchDashboardError"
    });
    return {
        leases: d,
        isLeasesLoading: c,
        search: e,
        filter: n,
        page: o,
        handleSearch: e => {
            t(e), s(1)
        },
        handleFilter: e => {
            r(e), s(1)
        },
        handleSort: e => {
            i({
                sortBy: e.sortBy,
                sortDirection: e.value
            }), s(1)
        },
        setPage: s,
        sort: a
    }
}
var e7, t7 = {};

function n7() {
    if (e7) return t7;
    e7 = 1;
    var e = h();
    Object.defineProperty(t7, "__esModule", {
        value: !0
    }), t7.default = void 0;
    var t = e(jp()),
        n = m();
    return t7.default = (0, t.default)((0, n.jsx)("path", {
        d: "M4.25 5.61C6.27 8.2 10 13 10 13v6c0 .55.45 1 1 1h2c.55 0 1-.45 1-1v-6s3.72-4.8 5.74-7.39c.51-.66.04-1.61-.79-1.61H5.04c-.83 0-1.3.95-.79 1.61"
    }), "FilterAlt"), t7
}
const r7 = It(n7()),
    a7 = ({
        title: t,
        handleFilter: n,
        filterValues: r,
        selectedFilters: a,
        isMultiple: i = !0
    }) => {
        const {
            t: o
        } = Gn(), s = t || o("common.Filter by"), [l, d] = Dt.useState(s), [c, u] = Dt.useState(!1), [p, h] = Dt.useState(a);
        Dt.useEffect(() => {
            h(a), d(a && a?.status?.length ? `${o("selected")} (${yZ(Object.values(a)).length})` : s)
        }, [a]);
        const m = ({
            checked: e,
            id: t,
            name: n
        }) => {
            const r = e => h(e);
            e ? i ? (() => {
                const e = p[n] || [];
                r({
                    ...p,
                    [n]: [...e, t]
                })
            })() : r({
                [n]: [t]
            }) : (() => {
                const e = {
                        ...p
                    },
                    a = e[n].filter(e => e !== t);
                0 === a.length ? delete e[n] : e[n] = a, r(e)
            })()
        };
        return e.jsx(cP, {
            row: !0,
            ycenter: !0,
            children: e.jsxs(lP, {
                sx: {
                    mr: {
                        xs: 2,
                        sm: 6
                    }
                },
                children: [e.jsx(r$, {
                    onClick: () => {
                        u(!0)
                    },
                    sx: {
                        backgroundColor: "#fff",
                        minWidth: {
                            xs: 50,
                            sm: 170
                        },
                        maxWidth: {
                            xs: 50,
                            sm: 170
                        },
                        color: "#232425",
                        border: "1px solid #E3E3E3 ",
                        fontWeight: Object.keys(p || {})?.length ? "700" : "400",
                        px: {
                            xs: 1,
                            sm: 4
                        }
                    },
                    children: e.jsxs(cP, {
                        sx: {
                            display: "flex",
                            alignItems: "center",
                            justifyContent: "center",
                            width: "100%",
                            columnGap: 1
                        },
                        children: [e.jsx(r7, {
                            sx: {
                                color: "#232425",
                                display: {
                                    xs: "inline-flex",
                                    sm: "none"
                                }
                            }
                        }), e.jsx(Np, {
                            sx: {
                                color: "#232425",
                                display: {
                                    xs: "none",
                                    sm: "inline-flex"
                                },
                                mr: {
                                    xs: 0,
                                    sm: 4
                                }
                            }
                        }), e.jsx(cP, {
                            component: "span",
                            sx: {
                                display: {
                                    xs: "none",
                                    sm: "inline"
                                }
                            },
                            children: l
                        }), e.jsx(zp, {
                            sx: {
                                display: {
                                    xs: "none",
                                    sm: "inline-flex"
                                }
                            }
                        })]
                    })
                }), e.jsxs(v, {
                    onClose: () => {
                        u(!1)
                    },
                    open: c,
                    sx: {
                        minWidth: "400px"
                    },
                    children: [e.jsx(b, {
                        sx: {
                            minWidth: "400px"
                        },
                        children: e.jsxs(sP, {
                            justifyContent: "space-between",
                            alignItems: "center",
                            children: [e.jsx(rP, {
                                variant: "h6",
                                sx: {
                                    fontWeight: "700",
                                    fontSize: "24px"
                                },
                                children: o("requests.filter.filter")
                            }), e.jsx(dP, {
                                onClick: () => {
                                    h({})
                                },
                                sx: {
                                    fontWeight: "700",
                                    fontSize: "14px",
                                    color: "#004256",
                                    cursor: "pointer"
                                },
                                children: o("common.delete_all")
                            })]
                        })
                    }), Object.keys(r).map((t, n) => e.jsxs(ft, {
                        defaultExpanded: !0,
                        style: {
                            boxShadow: "none",
                            margin: 0,
                            padding: "10px"
                        },
                        children: [e.jsx(gt, {
                            expandIcon: e.jsx(JU, {}),
                            "aria-controls": "panel1a-content",
                            id: `panel1a-header${n}`,
                            sx: {
                                margin: "0px !important"
                            },
                            children: e.jsx(rP, {
                                sx: {
                                    margin: "0px"
                                },
                                s: 14,
                                children: o(t)
                            })
                        }), e.jsx(vt, {
                            children: r[t].map(t => {
                                const n = i ? S : C;
                                return e.jsxs(e.Fragment, {
                                    children: [e.jsx(A, {
                                        sx: {
                                            fontWeight: 400
                                        },
                                        control: e.jsx(n, {
                                            checked: !!p?.[t?.name] && p?.[t?.name]?.includes(+t?.id),
                                            onChange: e => {
                                                m({
                                                    checked: e?.target?.checked,
                                                    id: +t?.id,
                                                    name: t?.name
                                                })
                                            },
                                            name: t?.name,
                                            value: t?.id
                                        }),
                                        label: e.jsx(rP, {
                                            sx: {
                                                margin: "0px",
                                                fontWeight: "400 !important"
                                            },
                                            s: 14,
                                            children: o(t?.label)
                                        })
                                    }), e.jsx("br", {})]
                                })
                            })
                        })]
                    }, t)), e.jsx(dP, {
                        variant: "contained",
                        onClick: () => {
                            n(p), u(!1), Object.keys(p).length ? d(`${o("selected")} (${yZ(Object.values(p)).length})`) : d(o("common.Filter by"))
                        },
                        sx: {
                            mb: 6,
                            mx: 6
                        },
                        children: o("requests.filter.apply")
                    })]
                })]
            })
        })
    };

function i7({
    sortValues: t,
    handleSort: n,
    selectedOption: r
}) {
    const {
        t: a
    } = Gn(), [i, o] = Vt.useState(!1);
    return e.jsxs(e.Fragment, {
        children: [e.jsx(r$, {
            onClick: () => {
                o(!0)
            },
            sx: {
                backgroundColor: "#fff",
                minWidth: {
                    xs: 50,
                    sm: 120
                },
                maxWidth: {
                    xs: 50,
                    sm: 120
                },
                color: "#232425",
                border: "1px solid #E3E3E3 ",
                py: "13px",
                fontWeight: "400",
                px: {
                    xs: 0,
                    sm: 2
                }
            },
            children: e.jsxs(cP, {
                sx: {
                    display: "flex",
                    alignItems: "center",
                    justifyContent: "center",
                    width: "100%",
                    columnGap: 1
                },
                children: [e.jsx(Np, {
                    fontSize: "small",
                    sx: {
                        color: "#555"
                    }
                }), e.jsx(cP, {
                    component: "span",
                    sx: {
                        display: {
                            xs: "none",
                            sm: "inline"
                        }
                    },
                    children: a("common.Sort")
                }), e.jsx(Bf, {
                    fontSize: "small",
                    sx: {
                        color: "#333",
                        display: {
                            xs: "none",
                            sm: "inline-flex"
                        }
                    }
                })]
            })
        }), e.jsxs(v, {
            onClose: () => o(!1),
            open: i,
            sx: {
                minWidth: "400px"
            },
            children: [e.jsx(b, {
                sx: {
                    minWidth: "400px",
                    mb: 0
                },
                children: e.jsxs(sP, {
                    justifyContent: "space-between",
                    alignItems: "center",
                    children: [e.jsx(rP, {
                        variant: "h6",
                        sx: {
                            fontWeight: "700",
                            fontSize: "24px"
                        },
                        children: a("common.Sort")
                    }), e.jsx(rP, {
                        variant: "caption",
                        sx: {
                            fontWeight: "700",
                            fontSize: "14px",
                            color: "#004256",
                            cursor: "pointer"
                        },
                        onClick: () => {
                            n({
                                sortBy: "",
                                value: ""
                            }), o(!1)
                        },
                        children: a("common.delete_all")
                    })]
                })
            }), e.jsx(cP, {
                mb: "2rem",
                column: !0,
                children: t?.map(t => e.jsxs(cP, {
                    row: !0,
                    sx: {
                        ml: "1rem",
                        mb: "0.6rem"
                    },
                    onClick: () => {
                        n(t), o(!1)
                    },
                    children: [r && e.jsx(C, {
                        checked: t.sortBy === r.sortBy && t.value === r.sortDirection
                    }), e.jsx(cP, {
                        sx: {
                            color: "#232425",
                            textAlign: "left",
                            justifyContent: "flex-start",
                            p: "14px 2px",
                            fontWeight: "400",
                            cursor: "pointer"
                        },
                        children: a(t.label)
                    })]
                }, t.value))
            })]
        })]
    })
}

function o7({
    sizes: t,
    RenderTable: n,
    ...r
}) {
    return e.jsx(e.Fragment, {
        children: e.jsx(BQ, {
            Section: n,
            ...r
        })
    })
}

function s7({
    page: t
}) {
    const {
        t: n
    } = Gn();
    return e.jsxs(cP, {
        mt: "2rem",
        width: "100%",
        textAlign: "center",
        children: [e.jsx(cP, {
            component: "img",
            sx: {
                display: "inline-block",
                mb: "0.5rem",