                    ...e,
                    key: e.title
                })), e.jsx("br", {}), c?.companyRepresentative && e.jsxs(e8, {
                    title: a("leasing.companyRepresentative"),
                    cols: u ? 7 : 5,
                    sx: {
                        padding: "16px",
                        gridColumn: "span 7",
                        "& .MuiTypography-root": {
                            fontSize: "16px !important",
                            marginBottom: "24px"
                        }
                    },
                    children: [c?.companyRepresentative?.filter(e => null != e)?.map(e => Dt.createElement(L8, {
                        ...e,
                        key: e.title
                    })), e.jsx(cP, {
                        row: !0,
                        width: "100%",
                        gridColumn: "span 7",
                        children: n?.tenant?.representative?.documents?.map(t => e.jsx(P8, {
                            file: t,
                            removeImage: () => {},
                            isDeleting: !0,
                            onFileClick: () => window.open(t.url, "__blank")
                        }, t.id))
                    })]
                })]
            }), e.jsx(w6, {
                sx: {
                    my: "24px"
                },
                children: e.jsxs(cP, {
                    children: [e.jsx(hp, {
                        variant: "h6",
                        children: a("dashboard.moveOut.overdueAmt")
                    }), e.jsxs(sP, {
                        children: [e.jsxs(lP, {
                            xs: 3,
                            sm: 3,
                            md: 3,
                            children: [e.jsx(hp, {
                                s: 12,
                                variant: "subtitle1",
                                light: !0,
                                gray: !0,
                                children: a("Total unpaid amounts")
                            }), e.jsx(hp, {
                                variant: "subtitle1",
                                currency: !0,
                                children: p
                            })]
                        }), e.jsxs(lP, {
                            xs: 6,
                            sm: 6,
                            md: 6,
                            children: [e.jsx(hp, {
                                s: 12,
                                variant: "subtitle1",
                                light: !0,
                                gray: !0,
                                children: a("Number of unpaid transactions")
                            }), e.jsx(hp, {
                                s: 16,
                                children: h
                            })]
                        }), e.jsx(cP, {
                            sx: {
                                mt: "20px"
                            },
                            ycenter: !0,
                            children: e.jsxs(hp, {
                                s: 16,
                                sx: {
                                    p: "14px",
                                    borderRadius: "8px",
                                    background: "#EBF0F1",
                                    display: "inherit",
                                    fontWeight: 400
                                },
                                children: [e.jsx(I5, {
                                    sx: {
                                        color: "#003748",
                                        mr: "8px"
                                    }
                                }), a("dashboard.moveOut.banner")]
                            })
                        })]
                    })]
                })
            }), e.jsxs(w6, {
                sx: {
                    my: "24px"
                },
                children: [e.jsx(lP, {
                    xs: 12,
                    sm: 12,
                    md: 12,
                    mb: "16px",
                    children: e.jsx(hp, {
                        s: 24,
                        children: a("dashboard.moveOut.securityDeposit")
                    })
                }), e.jsxs(sP, {
                    children: [e.jsxs(lP, {
                        xs: 6,
                        sm: 6,
                        md: 4,
                        lg: 4,
                        xl: 3,
                        children: [e.jsx(hp, {
                            s: 16,
                            light: !0,
                            children: a("Security deposit amount")
                        }), e.jsx(hp, {
                            s: 16,
                            light: !0,
                            mt: "4px",
                            children: a("Total deductions amount")
                        }), e.jsx(hp, {
                            s: 16,
                            sx: {
                                mt: "12px"
                            },
                            children: a("Refunded security deposit amount")
                        })]
                    }), e.jsxs(lP, {
                        xs: 6,
                        sm: 6,
                        md: 6,
                        children: [e.jsx(hp, {
                            light: !0,
                            currency: !0,
                            children: r
                        }), e.jsx(hp, {
                            light: !0,
                            currency: !0,
                            children: Math.abs(g ? -g : 0)
                        }), e.jsx(hp, {
                            currency: !0,
                            sx: {
                                mt: "12px"
                            },
                            children: Number(r) - Number(g)
                        })]
                    })]
                })]
            })]
        })
    },
    D7 = ({
        isMoveout: t = !1
    }) => {
        const [n, r] = Dt.useState(c7.Review_Records);
        return e.jsx(T7, {
            step: n,
            setStep: r,
            isMoveout: t,
            children: (() => {
                switch (n) {
                    case c7.Review_Records:
                        return e.jsx(k7, {
                            isMoveout: t
                        });
                    case c7.Security_Deposit:
                        return e.jsx(j7, {});
                    case c7.Review:
                        return e.jsx(E7, {
                            isMoveout: t
                        });
                    default:
                        return e.jsx(e.Fragment, {})
                }
            })()
        })
    };
var V7 = (e => (e[e.NEW = 11] = "NEW", e[e.WAITING = 12] = "WAITING", e[e.APPROVED = 13] = "APPROVED", e[e.REJECTED = 14] = "REJECTED", e[e.CANCELLED = 15] = "CANCELLED", e[e.CHECKED_IN = 16] = "CHECKED_IN", e[e.CHECKED_OUT = 17] = "CHECKED_OUT", e))(V7 || {});
const A7 = {
        "requests.filter.progress": [{
            name: "toolBooking.status.new",
            value: !0,
            id: "New",
            status: 11
        }, {
            name: "toolBooking.status.awaiting",
            value: !0,
            id: "Approved",
            status: 12
        }, {
            name: "toolBooking.status.checked_in",
            value: !0,
            id: "Approved",
            status: 16
        }]
    },
    O7 = {
        "requests.filter.progress": [{
            name: "toolBooking.status.reject",
            value: !0,
            id: "Rejected",
            status: 14
        }, {
            name: "toolBooking.status.cancel",
            value: !0,
            id: "Cancelled",
            status: 15
        }, {
            name: "toolBooking.status.checked_out",
            value: !0,
            id: "Checked-Out",
            status: 17
        }]
    },
    P7 = "history",
    I7 = "normal",
    F7 = async e => (await lo(`/api-management/rf/users/visitor-access/${e}`)).data, H7 = ({
        queryKey: e,
        type: t = "normal",
        userID: n,
        leaseId: r
    }) => {
        const {
            state: {
                page: a,
                search: i,
                filter: o
            },
            setFilter: s,
            setSearch: l,
            setPage: d
        } = AQ({
            enableSort: !1,
            defaultFilter: {
                status: []
            }
        }), c = tl([...e, a, i, o, o?.status, t, r], async () => await (async e => await lo("/api-management/rf/users/visitor-access", e))({
            page: a,
            query: i,
            status: o?.status || [],
            is_paginate: 1,
            type: t,
            user_id: n,
            rf_lease_id: r
        }));
        return {
            page: a,
            setPage: d,
            search: i,
            filter: o,
            status: o?.status || [],
            setStatus: e => {
                s({
                    ...o,
                    status: e
                })
            },
            handleSearch: l,
            visitorList: c?.data?.data?.list,
            lastPage: c?.data?.data?.paginator?.last_page,
            isLoading: c?.isLoading,
            refetch: c?.refetch
        }
    }, N7 = ({
        filtering: t,
        filterValues: n,
        status: r,
        setStatus: a
    }) => {
        const i = n,
            {
                t: o
            } = Gn(),
            [s, l] = Dt.useState(o("common.Filter by")),
            [d, c] = Dt.useState(!1),
            [u, p] = Dt.useState(r);
        Dt.useEffect(() => {
            p(r)
        }, [r]);
        return e.jsx(cP, {
            row: !0,
            ycenter: !0,
            children: t && e.jsxs(lP, {
                sx: {
                    mr: 6
                },
                children: [e.jsx(r$, {
                    onClick: () => {
                        c(!0)
                    },
                    style: {
                        backgroundColor: "#fff",
                        minWidth: "170px",
                        color: "#232425",
                        border: "1px solid #E3E3E3 "
                    },
                    children: e.jsxs(e.Fragment, {
                        children: [e.jsx(Np, {
                            sx: {
                                color: "#232425",
                                mr: 4
                            }
                        }), s, e.jsx(zp, {})]
                    })
                }), e.jsxs(v, {
                    onClose: () => {
                        c(!1)
                    },
                    open: d,
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
                            }), e.jsx(rP, {
                                variant: "caption",
                                sx: {
                                    fontWeight: "700",
                                    fontSize: "14px",
                                    color: "#004256",
                                    cursor: "pointer"
                                },
                                onClick: () => {
                                    a(u), c(!1)
                                },
                                children: o("requests.filter.apply")
                            })]
                        })
                    }), Object.keys(i).map((t, r) => e.jsx(e.Fragment, {
                        children: e.jsxs(ft, {
                            defaultExpanded: !0,
                            style: {
                                boxShadow: "none",
                                margin: 0,
                                padding: "10px"
                            },
                            children: [e.jsx(gt, {
                                expandIcon: e.jsx(JU, {}),
                                "aria-controls": "panel1a-content",
                                id: "panel1a-header" + r,
                                sx: {
                                    margin: "0px !important"
                                },
                                children: e.jsx(rP, {
                                    variant: "h5",
                                    sx: {
                                        margin: "0px"
                                    },
                                    children: o(t)
                                })
                            }), e.jsx(vt, {
                                children: n[t].map(t => e.jsxs(e.Fragment, {
                                    children: [e.jsx(A, {
                                        sx: {
                                            fontWeight: 400
                                        },
                                        control: e.jsx(S, {
                                            checked: u.includes(t?.status),
                                            onChange: e => {
                                                ((e, t) => {
                                                    let n = u;
                                                    e ? (n.push(t), p([...n])) : (n = n.filter(e => e !== t), p([...n]))
                                                })(e?.target?.checked, t?.status)
                                            },
                                            name: t.id,
                                            value: "sdfg"
                                        }),
                                        label: e.jsx(rP, {
                                            variant: "h6",
                                            sx: {
                                                margin: "0px",
                                                fontWeight: "400 !important"
                                            },
                                            children: o(t.name)
                                        })
                                    }), e.jsx("br", {})]
                                }))
                            })]
                        })
                    }))]
                })]
            })
        })
    };

function R7({
    requestId: t,
    queryKey: n,
    refetch: r
}) {
    const {
        t: a
    } = Gn(), i = Ys(), [o, s] = Dt.useState(!1), [l, d] = Dt.useState(!1), c = async () => {
        try {
            d(!0), await (async e => (await co(`/api-management/rf/users/visitor-access/${e}/approve`)).data)(t), r(), i.invalidateQueries(n), d(!1)
        } catch (e) {
            d(!1)
        }
    };
    return e.jsxs(e.Fragment, {
        children: [e.jsx(wp, {
            onClick: e => {
                e.preventDefault(), s(!0)
            },
            sx: {
                mr: "10px"
            },
            variant: "outlined",
            color: "error",
            disabled: l,
            children: a("common.reject")
        }), e.jsx(wp, {
            onClick: e => {
                e.preventDefault(), c()
            },
            sx: {
                mr: "10px"
            },
            variant: "contained",
            disabled: l,
            children: a("requests.approve")
        }), e.jsx(M5, {
            isOpen: o,
            handleClose: () => s(!1),
            queryKey: n,
            title: a("requests.rejectConfirmTitle"),
            body: a("requests.rejectConfirmBody"),
            confirmFunc: () => (async e => (await co(`/api-management/rf/users/visitor-access/${e}/reject`)).data)(t)
        })]
    })
}
const Y7 = e => q6(e, Jn),
    B7 = {
        [V7.NEW]: xU,
        [V7.WAITING]: CU,
        [V7.APPROVED]: bU,
        [V7.REJECTED]: wU,
        [V7.CANCELLED]: wU,
        [V7.CHECKED_IN]: bU,
        [V7.CHECKED_OUT]: _U
    };

function z7({
    request: t,
    queryKey: n,
    refetch: r
}) {
    const {
        t: a
    } = Gn(), {
        CurrentBrand: i
    } = Gc();
    return e.jsxs(U7, {
        request: t,
        children: [e.jsx(Pp, {
            xs: 12,
            md: 9,
            children: e.jsxs(sP, {
                ycenter: !0,
                gap: "20px",
                row: !0,
                mb: "10px",
                sx: {
                    flexWrap: "nowrap"
                },
                children: [e.jsx(Pp, {
                    children: e.jsx(Vp, {
                        name: wZ(t?.first_name, t?.last_name),
                        sx: {
                            width: "42px",
                            height: "42px"
                        },
                        backgroundColor: u(qc[i].primaryPalette.main, .9)
                    })
                }), e.jsxs(Pp, {
                    xs: 6,
                    xl: 2,
                    lg: 2,
                    children: [e.jsx(hp, {
                        s: 16,
                        sx: W7.txt,
                        color: "text.secondary",
                        children: wZ(t?.first_name, t?.last_name)
                    }), e.jsx(hp, {
                        s: 14,
                        bold: !0,
                        sx: {
                            display: "flex",
                            mt: "8px",
                            "line-break": "anywhere"
                        },
                        children: t?.unit_details?.unit_name || "--"
                    })]
                }), e.jsxs(Pp, {
                    xs: 6,
                    xl: 2.5,
                    lg: 2.5,
                    children: [e.jsx(hp, {
                        s: 12,
                        light: !0,
                        sx: {
                            color: "text.secondary"
                        },
                        children: a("qr.visitDateLabel")
                    }), e.jsx(hp, {
                        s: 16,
                        sx: {
                            fontWeight: 700,
                            display: "flex",
                            mt: "8px",
                            "line-break": "anywhere"
                        },
                        children: t?.visit_date ? Fj(t.visit_date).format("DD MMM,YYYY") : "--"
                    })]
                }), e.jsxs(Pp, {
                    xs: 6,
                    xl: 2.5,
                    lg: 2.5,
                    children: [e.jsx(hp, {
                        s: 12,
                        light: !0,
                        sx: {
                            color: "text.secondary"
                        },
                        children: a("qr.visitTime")
                    }), e.jsx(hp, {
                        s: 16,
                        sx: {
                            fontWeight: 700,
                            display: "flex",
                            mt: "8px",
                            "line-break": "anywhere"
                        },
                        children: !!t?.visit_time && Y7(t?.visit_time)
                    })]
                }), e.jsxs(Pp, {
                    xs: 6,
                    xl: 2,
                    lg: 2,
                    children: [e.jsx(hp, {
                        s: 12,
                        light: !0,
                        sx: {
                            color: "text.secondary"
                        },
                        children: a("headers.status")
                    }), e.jsx(hp, {
                        s: 15,
                        sx: {
                            fontWeight: 700,
                            display: "flex",
                            mt: "8px"
                        },
                        children: e.jsx(ih, {
                            variant: B7[t?.status_value] || "neutral",
                            title: t?.status
                        })
                    })]
                })]
            })
        }), +t?.status_value === V7.WAITING && e.jsx(Pp, {
            onClick: e => e.stopPropagation(),
            xs: 12,
            md: 3,
            mt: "10px",
            sx: {
                textAlign: "end"
            },
            children: e.jsx(R7, {
                queryKey: n,
                requestId: t?.id,
                refetch: r
            })
        })]
    })
}
const U7 = ({
        request: t,
        children: n
    }) => e.jsx(ap, {
        sx: W7.cardWrapper,
        children: e.jsx(ap, {
            component: Wt,
            to: `/visitor-access/visitor-details/${t?.id}`,
            children: e.jsx(et, {
                sx: W7.cardContent,
                children: e.jsx(sP, {
                    spacing: "70px",
                    row: !0,
                    mb: "10px",
                    children: n
                })
            })
        })
    }),
    W7 = {
        txt: {
            lineBreak: "anywhere",
            whiteSpace: "nowrap",
            overflow: "hidden",
            textOverflow: "ellipsis"
        },
        cardWrapper: {
            boxShadow: "0px 0px 21px rgba(218, 218, 218, 0.5)",
            borderRadius: "16px",
            "& a": {
                textDecoration: "none"
            }
        },
        cardContent: {
            position: "relative",
            borderRadius: "8px",
            backgroundColor: "#fff",
            alignItems: "center",
            justifyContent: "center",
            display: "flex",
            paddingBottom: "0px !important",
            transition: "0.3s all ease",
            "&:hover": {
                backgroundColor: "#eee",
                cursor: "pointer"
            }
        }
    },
    Z7 = ({
        showFilter: t = !0,
        header: n,
        showHistoryButton: r = !1,
        filterValues: a,
        queryKey: i,
        visitorList: o,
        lastPage: s,
        isLoading: l,
        page: d,
        search: c,
        status: u,
        onSearch: p,
        onStatusChange: h,
        onPageChange: m,
        refetch: f
    }) => {
        const g = !!o && !o.length;
        return e.jsxs(e.Fragment, {
            children: [n, e.jsx(q7, {
                search: c,
                handleSearch: p,
                showFilter: t,
                status: u,
                setStatus: h,
                showHistoryButton: r,
                filterValues: a
            }), l && e.jsx(Z2, {}), g && e.jsx(q2, {}), e.jsx(cP, {
                sx: {
                    minHeight: "80%"
                },
                children: o?.map(t => e.jsx(cP, {
                    width: "100%",
                    mb: "24px",
                    children: e.jsx(z7, {
                        request: t,
                        queryKey: i,
                        refetch: f
                    })
                }, t.id))
            }), e.jsx(HQ, {
                page: d,
                count: s,
                handler: m
            })]
        })
    },
    q7 = ({
        search: t,
        handleSearch: n,
        showFilter: r,
        status: a,
        setStatus: i,
        showHistoryButton: o,
        filterValues: s
    }) => {
        const {
            t: l
        } = Gn();
        return e.jsxs(cP, {
            fullWidth: !0,
            row: !0,
            xBetween: !0,
            mb: "24px",
            children: [e.jsxs(cP, {
                row: !0,
                fullWidth: !0,
                children: [e.jsx(RQ, {
                    search: t,
                    handleSearch: n,
                    height: "40px"
                }), r && e.jsx(N7, {
                    filtering: r,
                    filterValues: s,
                    status: a,
                    setStatus: i
                })]
            }), o && e.jsxs(wp, {
                component: Wt,
                to: "history",
                variant: "outlined",
                color: "primary",
                children: [e.jsx(gN, {
                    sx: {
                        marginRight: "10px"
                    }
                }), " ", l("requests.history")]
            })]
        })
    },
    $7 = ({
        type: t = I7,
        showFilter: n = !0,
        header: r,
        userID: a,
        leaseId: i
    }) => {
        const {
            page: o,
            setPage: s,
            search: l,
            filter: d,
            status: c,
            setStatus: u,
            handleSearch: p,
            visitorList: h,
            lastPage: m,
            isLoading: f,
            refetch: g
        } = H7({
            queryKey: [mF, a],
            type: t,
            userID: a,
            leaseId: i
        }), y = t === P7 ? O7 : A7, v = t !== P7;
        return e.jsx(Z7, {
            showFilter: n,
            header: r,
            showHistoryButton: v,
            filterValues: y,
            queryKey: [mF, o, l, d, c],
            refetch: g,
            visitorList: h,
            lastPage: m,
            isLoading: f,
            page: o,
            search: l,
            status: c,
            onSearch: p,
            onStatusChange: u,
            onPageChange: s
        })
    },
    G7 = ({
        userID: t,
        type: n,
        leaseId: r
    }) => e.jsx($7, {
        type: n || I7,
        userID: t,
        leaseId: r
    }),
    K7 = Object.freeze(Object.defineProperty({
        __proto__: null,
        default: G7
    }, Symbol.toStringTag, {
        value: "Module"
    })),
    Q7 = ({
        isHistory: t = !1,
        isLease: n = !1
    }) => {
        const r = qt().id,
            {
                t: a
            } = Gn();
        return e.jsxs(e.Fragment, {
            children: [e.jsx(ap, {
                my: "12px",
                children: e.jsx(IQ, {})
            }), e.jsx(hp, {
                variant: "h4",
                mb: "24px",
                children: a("breadcrumb.visitorAccess")
            }), e.jsx(G7, {
                leaseId: n ? +r : void 0,
                userID: n ? void 0 : r,
                type: t ? P7 : I7
            })]
        })
    },
    J7 = Object.freeze(Object.defineProperty({
        __proto__: null,
        default: Q7
    }, Symbol.toStringTag, {
        value: "Module"
    }));
var X7 = (e => (e.RENT = "rent", e.SALE = "sale", e))(X7 || {}),
    e9 = (e => (e[e.COMING_SOON = 1] = "COMING_SOON", e[e.AVAILABLE = 2] = "AVAILABLE", e[e.SOLD = 3] = "SOLD", e[e.RENTED = 4] = "RENTED", e))(e9 || {}),
    t9 = (e => (e[e.AVAILABLE = 1] = "AVAILABLE", e[e.SOLD = 2] = "SOLD", e[e.LEASED = 3] = "LEASED", e[e.BOOKED = 4] = "BOOKED", e))(t9 || {});
const n9 = (e, t) => [{
        id: 1,
        name: "marketplace.Available"
    }, t && "sale" === e && {
        id: 4,
        name: "marketplace.booked_for_sale"
    }, "rent" === e ? {
        id: 3,
        name: "Leased"
    } : {
        id: 2,
        name: "Sold"
    }].filter(Boolean),
    r9 = [{
        id: "1",
        name: "withMissing"
    }, {
        id: "0",
        name: "withoutMissing"
    }];
var a9 = (e => (e[e.UNLISTED = 0] = "UNLISTED", e[e.LISTED = 1] = "LISTED", e[e.OFF_PLAN = 2] = "OFF_PLAN", e[e.SALE_INFORMATION = 3] = "SALE_INFORMATION", e))(a9 || {});
const i9 = e => v1({
    area: o1().typeError(e("marketplace.areaType")).test("is-positive", e("marketplace.areaPositive"), e => e >= 0).min(1, e("marketplace.areaMoreThan")).test("is-decimal", e("marketplace.areaType"), e => /^\d+(\.\d{1,2})?$/.test(e.toString())).required(e("marketplace.areaRequired")),
    price: o1().typeError(e("marketplace.priceType")).test("is-positive", e("marketplace.pricePositive"), e => e >= 0).min(1, e("marketplace.priceMoreThan")).test("is-decimal", e("marketplace.priceType"), e => /^\d+(\.\d{1,2})?$/.test(e.toString())).required(e("marketplace.priceRequired")),
    deposit: o1().typeError(e("marketplace.depositType")).test("is-positive", e("marketplace.depositPositive"), e => e >= 0).min(1, e("marketplace.depositMoreThan")).test("is-decimal", e("marketplace.depositType"), e => /^\d+(\.\d{1,2})?$/.test(e.toString())).required(e("marketplace.depositRequired"))
});
var o9 = (e => (e[e.PRE_BOOKING_CREATED = 39] = "PRE_BOOKING_CREATED", e[e.PRE_BOOKING_APPROVED = 40] = "PRE_BOOKING_APPROVED", e[e.CANCELLED_BEFORE_DEPOSIT = 41] = "CANCELLED_BEFORE_DEPOSIT", e[e.BOOKING_REJECTED = 42] = "BOOKING_REJECTED", e[e.CANCELLED_AFTER_DEPOSIT = 43] = "CANCELLED_AFTER_DEPOSIT", e[e.DEPOSIT_PAID = 44] = "DEPOSIT_PAID", e[e.CONTRACT_SENT = 45] = "CONTRACT_SENT", e[e.COMPLETED_PAYMENT = 46] = "COMPLETED_PAYMENT", e[e.CONTRACT_SIGNED = 47] = "CONTRACT_SIGNED", e[e.OWNERSHIP_TRANSFERRED = 48] = "OWNERSHIP_TRANSFERRED", e))(o9 || {});
var s9 = (e => (e[e.SCHEDULED = 35] = "SCHEDULED", e[e.COMPLETED = 36] = "COMPLETED", e[e.CANCELLED = 37] = "CANCELLED", e[e.REJECTED = 38] = "REJECTED", e))(s9 || {});
const l9 = {
        36: "completed",
        35: "scheduled",
        37: "cancelled",
        38: "rejected"
    },
    d9 = {
        36: {
            bg: "#EDFAF4",
            txt: "#0A9458"
        },
        35: {
            bg: "#FCEDC7",
            txt: "#8A6A16"
        },
        37: {
            bg: "#FFE5E5",
            txt: "#812222"
        },
        38: {
            bg: "#FFE5E5",
            txt: "#812222"
        }
    };
var c9 = (e => (e[e.SUNDAY = 0] = "SUNDAY", e[e.MONDAY = 1] = "MONDAY", e[e.TUESDAY = 2] = "TUESDAY", e[e.WEDNESDAY = 3] = "WEDNESDAY", e[e.THURSDAY = 4] = "THURSDAY", e[e.FRIDAY = 5] = "FRIDAY", e[e.SATURDAY = 6] = "SATURDAY", e))(c9 || {});
const u9 = (e = []) => e?.map(e => c9?.[e?.toUpperCase()]),
    p9 = e => v1().shape({
        note: a1().required(e("rejectReasonRequired")).max(1e3, e("maxNumberOfCharacters", {
            number: "1,000"
        }))
    }),
    h9 = e => [{
        id: s9.SCHEDULED,
        name: e("scheduled")
    }, {
        id: s9.COMPLETED,
        name: e("completed")
    }, {
        id: s9.CANCELLED,
        name: e("cancelled")
    }, {
        id: s9.REJECTED,
        name: e("rejected")
    }];
o9.PRE_BOOKING_CREATED, o9.PRE_BOOKING_APPROVED, o9.BOOKING_REJECTED, o9.CANCELLED_BEFORE_DEPOSIT, o9.CANCELLED_AFTER_DEPOSIT, o9.DEPOSIT_PAID, o9.CONTRACT_SENT, o9.COMPLETED_PAYMENT, o9.CONTRACT_SIGNED, o9.OWNERSHIP_TRANSFERRED;

function m9({
    visitId: e,
    handleClose: t = () => {},
    isSales: n = !1
}) {
    const r = Ys(),
        {
            state: {
                filter: a,
                page: i,
                search: o
            },
            setFilter: s,
            setPage: l,
            setSearch: d
        } = AQ({
            enableSort: !1,
            defaultFilter: {
                status: {},
                chosenCommunity: {
                    id: null,
                    name: null
                }
            }
        }),
        {
            t: c
        } = Gn(),
        {
            data: u,
            isLoading: p,
            refetch: h
        } = tl([DH, n, {
            page: i,
            search: o,
            filter: a.status,
            chosenCommunity: a.chosenCommunity
        }], async () => await (async ({
            search: e,
            filter: t,
            page: n,
            chosenCommunity: r,
            isSales: a
        }) => {
            try {
                return i = await lo("/api-management/marketplace/admin/visits", {
                    search: e,
                    page: n,
                    status_id: t,
                    community_id: r?.id,
                    is_paginate: 1,
                    limit: 30,
                    type: a ? 2 : 1
                }), {
                    list: i?.data?.list?.map(e => ({
                        id: e.id,
                        date: e?.date_time,
                        name: e?.user?.name,
                        phone: e?.user?.phone_number,
                        community: e?.community?.name,
                        building: e?.building ? e?.building?.name : null,
                        unit: e?.unit?.name,
                        visitOwner: {
                            name: e?.visit_owner?.name,
                            phone: e?.visit_owner?.phone_number
                        },
                        status: {
                            id: e?.status?.id,
                            name: e?.status?.name
                        }
                    })) || [],
                    total: i?.data?.paginator?.total || 0,
                    page: i?.data?.paginator?.current_page || 1,
                    pagesCount: i?.data?.paginator?.last_page || 1
                }
            } catch (o) {
                throw o
            }
            var i
        })({
            page: i,
            search: o,
            filter: a.status,
            chosenCommunity: a.chosenCommunity,
            isSales: n
        }), {
            useErrorBoundary: !1
        }),
        {
            data: m,
            isLoading: f
        } = tl([DH, e], async () => await (async ({
            visitId: e
        }) => {
            try {
                const n = await lo(`/api-management/marketplace/admin/visits/${e}`);
                return t = n?.data, {
                    visitId: t?.id,
                    communityName: t?.community?.name,
                    buildingName: t?.building ? t?.building.name : "---",
                    unitName: t?.unit?.name,
                    dateTime: t?.date_time,
                    visitorDetails: {
                        name: t?.user?.name,
                        phoneNumber: t?.user?.phone_number
                    },
                    scheduledBy: {
                        name: t.scheduled_by?.name,
                        phoneNumber: t?.scheduled_by?.phone_number
                    },
                    visitOwner: {