                        name: t?.visit_owner?.name,
                        phoneNumber: t?.visit_owner?.phone_number
                    },
                    completedBy: {
                        name: t.completed_by?.name,
                        phoneNumber: t?.completed_by?.phone_number
                    },
                    cancelledBy: {
                        name: t.canceled_by?.name,
                        phoneNumber: t?.canceled_by?.phone_number
                    },
                    rejectedBy: {
                        name: t.rejected_by?.name,
                        phoneNumber: t?.rejected_by?.phone_number
                    },
                    status: {
                        id: t?.status?.id,
                        name: t?.status?.name
                    },
                    rejectedReason: t?.rejected_reason,
                    cancelledReason: t?.canceled_reason
                }
            } catch (n) {
                throw n
            }
            var t
        })({
            visitId: e
        }), {
            enabled: !!e,
            useErrorBoundary: !1
        }),
        {
            isLoading: g,
            mutate: y,
            isError: v
        } = nl(() => (async e => {
            try {
                const t = {
                    rf_unit_id: e?.unitId,
                    day: e?.day,
                    time: e?.time,
                    user_id: e?.userId,
                    type: e?.type
                };
                await co("/api-management/marketplace/admin/visits", t)
            } catch (t) {
                throw t
            }
        })({
            ..._?.getValues(),
            type: n ? 2 : 1
        }), {
            retry: !1,
            onSuccess: () => {
                Zi.success(c("visitCreatedSuccessfully"), {
                    toastId: "createCustomerSuccess"
                }), t(), _.reset(), h()
            }
        }),
        _ = bf({
            defaultValues: {
                rf_unit_id: null,
                day: "",
                time: "",
                user_id: null
            },
            mode: "onChange",
            reValidateMode: "onChange",
            resolver: L1(v1().shape({
                rf_unit_id: o1().required(),
                day: a1().required(),
                time: a1().required(),
                user_id: o1().required()
            }))
        }),
        {
            handleSubmit: x,
            formState: {
                errors: b
            },
            control: w
        } = _;
    return {
        data: u,
        search: o,
        filter: a.status ? Number(a.status) : null,
        page: i,
        isLoading: p,
        setSearch: d,
        setFilter: e => {
            e && s({
                ...a,
                status: e && !isNaN(Number(e)) ? Number(e) : null
            })
        },
        setPage: l,
        chosenCommunity: {
            id: Number(a.chosenCommunity?.id) || null,
            name: a.chosenCommunity?.name || null
        },
        setChosenCommunity: e => {
            s({
                ...a,
                chosenCommunity: e
            })
        },
        form: _,
        submitHandler: () => {
            y()
        },
        isError: v,
        errors: b,
        control: w,
        isCreating: g,
        visitDetails: m,
        isLoadingVisitDetails: f,
        handleAssignVisitOwner: async ({
            itemID: e,
            userId: t
        }) => {
            await (async ({
                visitId: e,
                userId: t
            }) => {
                try {
                    return (await co(`/api-management/marketplace/admin/visits/assign/owner-visit/${e}`, {
                        visit_owner_id: t
                    })).data
                } catch (n) {
                    throw n
                }
            })({
                visitId: e,
                userId: t
            }), r.invalidateQueries([DH])
        }
    }
}

function f9({
    visitId: e = null,
    handleClose: t = () => {},
    setOpenModal: n
}) {
    const {
        t: r
    } = Gn(), a = Ys(), {
        getValues: i,
        handleSubmit: o,
        control: s,
        formState: {
            errors: l,
            isSubmitting: d
        },
        reset: c
    } = bf({
        resolver: L1(p9(r)),
        defaultValues: {
            note: ""
        }
    }), u = nl({
        mutationFn: () => (async ({
            visitId: e,
            reason: t
        }) => {
            try {
                return (await co(`/api-management/marketplace/admin/visits/rejected/${e}`, {
                    reason: t
                })).data
            } catch (n) {
                throw n
            }
        })({
            visitId: e,
            reason: i("note")
        }),
        onSuccess: () => {
            a.invalidateQueries([DH, e]), Zi.success(r("visitRejectedSuccessfully")), t(), c(), a.invalidateQueries([DH])
        }
    }), p = nl({
        mutationFn: () => (async ({
            visitId: e
        }) => {
            try {
                return (await co(`/api-management/marketplace/admin/visits/completed/${e}`)).data
            } catch (t) {
                throw t
            }
        })({
            visitId: e
        }),
        onSuccess: () => {
            a.invalidateQueries([DH, e]), Zi.success(r("visitCompletedSuccessfully")), t(), n(!1), a.invalidateQueries([DH])
        }
    });
    return {
        onClose: () => {
            t(), c()
        },
        onReject: o(() => {
            u.mutate()
        }),
        onComplete: () => {
            p.mutate()
        },
        errors: l,
        control: s,
        isLoading: u.isLoading
    }
}
const g9 = ({
        visitId: t,
        open: n = !1,
        handleClose: r,
        onRejectPress: a,
        onModify: i
    }) => {
        if (!n) return null;
        const [o, s] = Dt.useState(!1), {
            t: l
        } = Gn(), {
            onComplete: c
        } = f9({
            visitId: t,
            handleClose: i,
            setOpenModal: s
        }), {
            visitDetails: u,
            isLoadingVisitDetails: p
        } = m9({
            visitId: t
        }), h = u?.status?.id == s9.SCHEDULED, m = (e => {
            const t = e?.status?.id == s9.COMPLETED,
                n = e?.status?.id == s9.CANCELLED,
                r = e?.status?.id == s9.REJECTED;
            return [{
                items: [{
                    label: "visitId",
                    value: e?.visitId
                }, {
                    label: "scheduleDateAndTime",
                    value: e?.dateTime,
                    isRtl: !0
                }, {
                    label: "communityName",
                    value: e?.communityName
                }, {
                    label: "buildingName",
                    value: e?.buildingName ?? "--"
                }, {
                    label: "unitName",
                    value: e?.unitName
                }]
            }, {
                title: "visitorDetails",
                items: [{
                    label: "name",
                    value: e?.visitorDetails?.name
                }, {
                    label: "phoneNumber",
                    value: e?.visitorDetails?.phoneNumber,
                    isRtl: !0
                }]
            }, e?.visitOwner?.name ? {
                title: "visitOwner",
                items: [{
                    label: "name",
                    value: e?.visitOwner?.name
                }, {
                    label: "phoneNumber",
                    value: e?.visitOwner?.phoneNumber,
                    isRtl: !0
                }]
            } : {}, {
                title: "scheduledBy",
                items: [{
                    label: "name",
                    value: e?.scheduledBy?.name
                }, {
                    label: "phoneNumber",
                    value: e?.scheduledBy?.phoneNumber,
                    isRtl: !0
                }]
            }, t ? {
                title: "completedBy",
                items: [{
                    label: "name",
                    value: e?.completedBy?.name
                }, {
                    label: "phoneNumber",
                    value: e?.completedBy?.phoneNumber,
                    isRtl: !0
                }]
            } : {}, n ? {
                title: "cancellationReason",
                text: e?.cancelledReason ?? "--"
            } : {}, n ? {
                title: "cancelledBy",
                items: [{
                    label: "name",
                    value: e?.cancelledBy?.name
                }, {
                    label: "phoneNumber",
                    value: e?.cancelledBy?.phoneNumber,
                    isRtl: !0
                }]
            } : {}, r ? {
                title: "rejectionReason",
                text: e?.rejectedReason ?? "--"
            } : {}, r ? {
                title: "rejectedBy",
                items: [{
                    label: "name",
                    value: e?.rejectedBy?.name
                }, {
                    label: "phoneNumber",
                    value: e?.rejectedBy?.phoneNumber,
                    isRtl: !0
                }]
            } : {}].filter(e => Object.keys(e).length)
        })(u);
        return e.jsxs(e.Fragment, {
            children: [e.jsx(v, {
                open: n,
                onClose: r,
                maxWidth: "md",
                fullWidth: !0,
                children: e.jsxs(_, {
                    children: [e.jsxs(cP, {
                        component: "header",
                        sx: {
                            display: "flex",
                            justifyContent: "space-between",
                            alignItems: "center",
                            marginBottom: "10px"
                        },
                        children: [e.jsxs(cP, {
                            sx: {
                                display: "flex",
                                alignContent: "center",
                                alignSelf: "center",
                                justifyContent: "center",
                                alignItems: "center"
                            },
                            children: [e.jsx(rP, {
                                sx: {
                                    margin: "16px 10px 8px 0px"
                                },
                                s: 26,
                                children: l("visitDetails")
                            }), l9[u?.status?.id] ? e.jsx(ve, {
                                label: l(l9[u?.status?.id]),
                                sx: {
                                    bgcolor: d9[u?.status?.id]?.bg,
                                    color: d9[u?.status?.id]?.txt
                                }
                            }) : null]
                        }), e.jsx(w, {
                            "aria-label": "close",
                            onClick: r,
                            children: e.jsx(ph, {})
                        })]
                    }), p ? e.jsx(cP, {
                        center: !0,
                        sx: {
                            minHeight: "200px"
                        },
                        children: e.jsx(d, {})
                    }) : m?.map(({
                        title: t,
                        items: n,
                        text: r
                    }) => e.jsxs(cP, {
                        sx: {
                            my: "12px",
                            px: "16px",
                            py: "16px",
                            borderRadius: 4,
                            border: "1px solid #E3E3E3"
                        },
                        children: [e.jsx(rP, {
                            variant: "h6",
                            sx: {
                                mb: "4px",
                                fontWeight: "bold",
                                textAlign: "start"
                            },
                            children: l(t)
                        }), !h && e.jsx(rP, {
                            s: "16",
                            light: !0,
                            children: r
                        }), e.jsx(cP, {
                            sx: {
                                display: "grid",
                                gridTemplateColumns: "repeat(3, 1fr)",
                                gridGap: "3rem"
                            },
                            children: n?.map(t => e.jsxs(cP, {
                                children: [e.jsx(rP, {
                                    s: "12",
                                    light: !0,
                                    gray: !0,
                                    children: l(t.label)
                                }), e.jsx(rP, {
                                    s: "16",
                                    light: t.fullWidth,
                                    dir: "ltr",
                                    sx: {
                                        textAlign: "left"
                                    },
                                    children: t.value || "---"
                                })]
                            }, t.label))
                        })]
                    })), ni.can(qI.Update, $I.Visits) && e.jsx(M, {
                        sx: {
                            mt: 0,
                            p: 0
                        },
                        children: h && e.jsx(cP, {
                            sx: {
                                display: "flex",
                                gap: "1rem"
                            },
                            children: tR(u?.dateTime, "DD/MM/YYYY, hh:mm A").isBefore(tR()) ? e.jsxs(cP, {
                                row: !0,
                                sx: {
                                    textAlign: "right",
                                    margin: "16px 0 0.5rem"
                                },
                                children: [e.jsx(dP, {
                                    size: "large",
                                    variant: "text",
                                    color: "error",
                                    disabled: p,
                                    onClick: a,
                                    sx: {
                                        width: "180px"
                                    },
                                    children: l("common.reject")
                                }), e.jsx(dP, {
                                    variant: "contained",
                                    disabled: p,
                                    onClick: () => s(!0),
                                    sx: {
                                        width: "200px"
                                    },
                                    children: l("complete")
                                })]
                            }) : e.jsx(dP, {
                                size: "large",
                                variant: "text",
                                color: "error",
                                disabled: p,
                                onClick: a,
                                sx: {
                                    width: "180px"
                                },
                                children: l("common.reject")
                            })
                        })
                    })]
                })
            }), e.jsx(QW, {
                content: {
                    title: l("visitCompleted") + "!",
                    body: l("visitCompleteSubtitle"),
                    errors: [],
                    actionText: l("common.no")
                },
                icon: e.jsx(KH, {
                    width: "100%",
                    sx: {
                        width: "70px",
                        height: "70px",
                        borderRadius: "50%"
                    }
                }),
                onDialogClose: () => s(!1),
                isOpen: !!o,
                clickAction: () => s(!1),
                primaryButton: {
                    title: l("common.yes"),
                    color: "primary.main",
                    handleClick: c
                }
            })]
        })
    },
    y9 = ({
        visitId: t,
        open: n = !1,
        handleClose: r,
        onModify: i
    }) => {
        const {
            t: o
        } = Gn(), {
            onClose: s,
            onReject: l,
            errors: d,
            control: c
        } = f9({
            visitId: t,
            handleClose: i
        });
        return e.jsx(v, {
            open: n,
            onClose: s,
            maxWidth: "sm",
            children: e.jsxs(_, {
                children: [e.jsx(a, {
                    component: "header",
                    sx: {
                        display: "flex",
                        justifyContent: "space-between",
                        alignItems: "center",
                        marginBottom: "10px",
                        width: "580px",
                        overflow: "hidden"
                    },
                    children: e.jsx(a, {
                        sx: {
                            display: "flex",
                            alignContent: "center",
                            alignSelf: "center",
                            justifyContent: "center",
                            alignItems: "center"
                        },
                        children: e.jsx(rP, {
                            sx: {
                                margin: "16px 10px 8px 0px"
                            },
                            s: 26,
                            children: o("rejectVisit")
                        })
                    })
                }), e.jsx(o$, {
                    control: c,
                    errors: d,
                    placeholder: o("typeYourReason"),
                    name: "note",
                    rows: 5,
                    multiline: !0
                }), e.jsx(M, {
                    sx: {
                        mt: 0,
                        p: 0
                    },
                    children: e.jsxs(a, {
                        row: !0,
                        sx: {
                            textAlign: "right",
                            margin: "16px 0 0.5rem"
                        },
                        children: [e.jsx(dP, {
                            size: "large",
                            variant: "text",
                            onClick: r,
                            sx: {
                                width: "180px"
                            },
                            children: o("cancel")
                        }), e.jsx(dP, {
                            variant: "contained",
                            onClick: l,
                            name: "SAVE_BTN",
                            sx: {
                                width: "200px"
                            },
                            children: o("submit")
                        })]
                    })
                })]
            })
        })
    };
var v9, _9 = {};

function x9() {
    if (v9) return _9;
    v9 = 1;
    var e = h();
    Object.defineProperty(_9, "__esModule", {
        value: !0
    }), _9.default = void 0;
    var t = e(jp()),
        n = m();
    return _9.default = (0, t.default)((0, n.jsx)("path", {
        d: "M3 17.25V21h3.75L17.81 9.94l-3.75-3.75zM20.71 5.63l-2.34-2.34a.9959.9959 0 0 0-1.41 0l-1.83 1.83 3.75 3.75 1.83-1.83c.39-.39.39-1.02 0-1.41"
    }), "ModeEditOutline"), _9
}
const b9 = It(x9());

function w9({
    data: t,
    setAssignVisitOwnerOpen: n
}) {
    const {
        t: r
    } = Gn(), [a, i] = Dt.useState(!1), [o, s] = Dt.useState(!1), [l] = $t(), d = l.get("id");
    Dt.useEffect(() => {
        d && Number(d) === t?.id && i(!0)
    }, [d, t?.id]);
    const c = () => {
            s(!1), i(!0)
        },
        p = () => {
            i(!1), s(!1)
        };
    return e.jsxs(e.Fragment, {
        children: [e.jsxs(uP, {
            children: [e.jsx(pP, {
                component: "td",
                scope: "row",
                children: e.jsx(hp, {
                    bold: !0,
                    children: t?.id
                })
            }), e.jsxs(pP, {
                component: "td",
                children: [e.jsx(hp, {
                    capitalize: !0,
                    variant: "smallText",
                    display: "block",
                    minWidth: "110px",
                    children: t?.name
                }), e.jsx(hp, {
                    variant: "smallText",
                    color: "text.secondary",
                    dir: "ltr",
                    children: t?.phone
                })]
            }), e.jsx(pP, {
                component: "td",
                scope: "row",
                children: e.jsxs(ap, {
                    row: !0,
                    gap: "2px",
                    ycenter: !0,
                    children: [e.jsx(w, {
                        sx: {
                            width: "48px",
                            height: "48px"
                        },
                        onClick: () => {
                            n(t?.id)
                        },
                        children: e.jsx(b9, {
                            color: "primary"
                        })
                    }), e.jsxs(ap, {
                        children: [e.jsx(hp, {
                            capitalize: !0,
                            variant: "smallText",
                            display: "block",
                            minWidth: "110px",
                            children: t?.visitOwner?.name ?? r("noassignee")
                        }), e.jsx(hp, {
                            variant: "caption",
                            color: "text.secondary",
                            dir: "ltr",
                            children: t?.visitOwner?.phone
                        })]
                    })]
                })
            }), e.jsx(pP, {
                component: "td",
                scope: "row",
                children: e.jsx(hp, {
                    capitalize: !0,
                    variant: "smallText",
                    children: t?.community ?? "--"
                })
            }), e.jsx(pP, {
                component: "td",
                scope: "row",
                children: e.jsx(hp, {
                    capitalize: !0,
                    variant: "smallText",
                    display: "block",
                    minWidth: "110px",
                    children: t?.building ?? "--"
                })
            }), e.jsx(pP, {
                component: "td",
                scope: "row",
                children: e.jsx(hp, {
                    capitalize: !0,
                    variant: "smallText",
                    children: t?.unit
                })
            }), e.jsx(pP, {
                component: "td",
                scope: "row",
                children: e.jsx(hp, {
                    variant: "smallText",
                    dir: "ltr",
                    sx: {
                        width: "160px",
                        display: "block"
                    },
                    children: t?.date ?? "--"
                })
            }), e.jsx(pP, {
                component: "td",
                scope: "row",
                children: e.jsx(ve, {
                    label: r(l9[t?.status?.id]),
                    sx: {
                        bgcolor: d9[t?.status?.id]?.bg,
                        color: d9[t?.status?.id]?.txt
                    }
                })
            }), e.jsx(pP, {
                children: e.jsx(dP, {
                    sx: {
                        backgroundColor: e => u(e.palette.primary.main, .1),
                        color: e => e.palette.primary.main,
                        width: "130px",
                        height: "46px",
                        fontSize: "12px",
                        px: "16px",
                        "&:hover": {
                            backgroundColor: e => `${e.palette.primary.main}8`,
                            color: e => e.palette.primary.main
                        }
                    },
                    onClick: () => {
                        i(!0)
                    },
                    isLoading: !1,
                    children: r("viewDetails")
                })
            })]
        }), a && e.jsx(g9, {
            visitId: t?.id,
            open: a,
            handleClose: p,
            onModify: c,
            onRejectPress: () => {
                i(!1), s(!0)
            }
        }), o && e.jsx(y9, {
            visitId: t?.id,
            open: o,
            handleClose: p,
            onModify: c
        })]
    })
}

function C9({
    setIsCreateFormOpen: t
}) {
    const {
        t: n
    } = Gn();
    return e.jsx(cP, {
        row: !0,
        ycenter: !0,
        children: e.jsx(dP, {
            sx: {
                mx: 2,
                my: 2,
                color: "white",
                maxHeight: "45px",
                width: "160px"
            },
            onClick: () => {
                t(!0)
            },
            variant: "contained",
            children: n("scheduleVisit")
        })
    })
}

function M9({
    openCommunitySelector: t,
    chosenCommunity: n
}) {
    const {
        t: r
    } = Gn();
    return e.jsx(cP, {
        column: !0,
        gap: "8px",
        children: e.jsxs(cP, {
            xbetween: !0,
            sx: {
                border: "1px solid #E5E5E5",
                borderRadius: 2,
                padding: "8px 16px",
                cursor: "pointer",
                width: "100%",
                height: "100%",
                backgroundColor: "white",
                "&:hover": {
                    backgroundColor: "#F5F5F5"
                }
            },
            onClick: () => {
                t()
            },
            children: [e.jsx(Np, {
                sx: {
                    color: "#232425",
                    mr: 4
                }
            }), e.jsx(rP, {
                s: 16,
                light: !0,
                sx: {
                    overflow: "hidden",
                    textOverflow: "ellipsis",
                    whiteSpace: "nowrap"
                },
                children: n?.name ?? r("customers.interestedCommunities")
            }), e.jsx(Bf, {
                color: "action",
                sx: {
                    width: 20,
                    height: 20,
                    margin: "0 5px"
                }
            })]
        })
    })
}
const S9 = e => e?.data?.list?.map(e => ({
        ...e,
        id: e.id,
        name: e.name,
        city: e.city.name,
        district: e.district.name,
        sales_commission_rate: e.sales_commission_rate
    })),
    L9 = async e => {
        try {
            const n = await lo("/api-management/marketplace/admin/communities?is_paginate=1&is_market_place=1", e);
            return t = n, {
                list: t?.data?.list?.map(e => ({
                    id: e.id,
                    name: e.name,
                    city: e.city?.name,
                    district: e.district?.name,
                    interests: e.no_interests,
                    active_bookings_count: e.active_bookings_count,
                    list_for: "1" == e.is_buy ? X7.SALE : X7.RENT,
                    units: e.no_listed_unit,
                    is_off_plan_sale: e.is_off_plan_sale,
                    status: {
                        id: e.status?.id,
                        name: e.status?.name
                    }
                })),
                metadata: {
                    total: t?.data?.paginator?.total,
                    last_page: t?.data?.paginator?.last_page
                }
            }
        } catch (n) {
            throw n
        }
        var t
    }, k9 = async ({
        page: e
    }) => {
        try {
            const t = await lo("/api-management/marketplace/admin/communities?is_paginate=1&is_market_place=0", {
                page: e
            });
            return S9(t)
        } catch (t) {
            throw t
        }
    }, T9 = async e => {
        try {
            const t = await lo("/api-management/marketplace/admin/communities?is_paginate=1&is_market_place=0&is_off_plan_sale=0", {
                page: e.page,
                query: e.search || ""
            });
            return S9(t)
        } catch (t) {
            throw t
        }
    };

function j9({
    visits: t,
    search: n,
    filterValues: r,
    selectedFilters: a,
    page: i,
    isLoading: o,
    setSearch: s,
    setFilter: l,
    chosenCommunity: d,
    setChosenCommunity: c,
    setPage: u,
    pagesCount: p,
    setIsCreateFormOpen: h,
    setAssignVisitOwnerOpen: m
}) {
    const {
        t: f
    } = Gn(), [g, y] = Dt.useState(!1);
    return e.jsxs(e.Fragment, {
        children: [e.jsx(o7, {
            RenderTable: e.jsx(E5, {
                isLoading: o,
                isEmpty: !t?.list?.length,
                noDataTitle: f("noVisitsTitle") + "!",
                noDataBody: f("noVisitsSubtitle"),
                bottomPagination: e.jsx(HQ, {
                    page: i,
                    count: p,
                    handler: u
                }),
                filters: e.jsxs(cP, {
                    row: !0,
                    gap: "16px",
                    children: [e.jsx(RQ, {
                        search: n,
                        handleSearch: s
                    }), e.jsx(M9, {
                        openCommunitySelector: () => y(!0),
                        chosenCommunity: d
                    }), e.jsx(hh, {
                        filters: r,
                        handleFilter: l,
                        handleReset: () => l({}),
                        selectedFilter: a,
                        title: f("filter")
                    })]
                }),
                pagination: ni.can(qI.Create, $I.Visits) ? e.jsx(C9, {
                    setIsCreateFormOpen: h
                }) : null,
                headerData: [f("visitId"), f("visitor"), f("visitOwner"), f("community"), f("building"), f("unit"), f("scheduleTime"), f("statusTitle"), ""],
                children: t?.list?.map(t => e.jsx(w9, {
                    data: t,
                    setAssignVisitOwnerOpen: m
                }, t.id))
            })
        }), e.jsx(Ch, {
            isOpen: g,
            onClose: () => y(!1),
            onSave: () => y(!1),
            fetcher: async ({
                search: e,
                page: t
            }) => {
                const n = await L9({
                    query: e,
                    page: t,
                    active: 1
                });
                return n?.list
            },
            setProperties: e => c(e?.[0]),
            title: f("selectCommunity"),
            refetchKey: d?.id,
            renderSubtitle: t => e.jsx(rP, {
                s: 12,
                light: !0,
                children: `${t?.city}${t?.city&&","} ${t?.district}`
            }),
            isMultiSelect: !1,
            chosenProperties: d ? [d] : [],
            clearBtnText: f("common.clear"),
            clearBtnStyles: {
                color: "error.main"
            }
        })]
    })
}
const E9 = ({
        subCategoryId: t,
        isOpen: n,
        setIsOpen: r,
        date: a,
        setDate: i,
        successFunc: o,
        startTime: s = "00:00",
        endTime: l = "23:00",
        isAllDay: c,
        isLoading: u,
        workingDays: p = [0, 1, 2, 3, 4]
    }) => {
        const {
            t: h
        } = Gn(), [m, f] = Dt.useState(() => Fj().startOf("month").format("YYYY-MM-DD")), [g, y] = Dt.useState([]), {
            data: v,
            isLoading: _
        } = tl({
            queryKey: [RF, t, m],