                p: "0.2rem",
                filter: "grayscale(0.9)"
            },
            src: j5
        }), e.jsx(rP, {
            s: "24",
            sx: {
                textTransform: "capitalize"
            },
            children: n("leasing.noDataAvailable_title")
        }), e.jsx(rP, {
            s: "16",
            light: !0,
            width: "304px",
            mx: "auto",
            mb: "1rem",
            children: n("dashboard" === t ? "leasing.noDataAvailable_body" : "leasing.noArchiveDataAvailable_body")
        })]
    })
}

function l7({
    leases: t,
    search: n,
    filterValues: r,
    selectedFilters: a,
    sortValues: i,
    page: o,
    isLoading: s,
    setSearch: d,
    setFilter: c,
    setSort: u,
    setPage: p,
    pagesCount: h,
    sort: m,
    isArchive: f
}) {
    const {
        t: g
    } = Gn(), y = Ft();
    return e.jsx(o7, {
        RenderTable: e.jsx(E5, {
            isLoading: s,
            isEmpty: !t?.list?.length,
            showEmptyPlaceholder: !0,
            emptyPlaceholder: e.jsx(s7, {
                page: "dashboard"
            }),
            bottomPagination: e.jsx(HQ, {
                page: o,
                count: h,
                handler: p
            }),
            filters: e.jsxs(cP, {
                row: !0,
                gap: "16px",
                children: [e.jsx(cP, {
                    sx: {
                        "& fieldset": {
                            border: "none"
                        },
                        "& input": {
                            py: "6px"
                        }
                    },
                    children: e.jsx(RQ, {
                        search: n,
                        handleSearch: d
                    })
                }), e.jsx(a7, {
                    filterValues: r,
                    handleFilter: c,
                    selectedFilters: a
                }), e.jsx(i7, {
                    sortValues: i,
                    handleSort: u,
                    selectedOption: m
                })]
            }),
            headerData: [g("leasing.leaseNumber"), g("leasing.tenant"), g("leasing.unit"), g("leasing.building"), g("leasing.community"), g("requests.status"), g(f ? "leasing.lastModified" : "leasing.daysRemaining"), ""],
            children: t?.list?.map(t => e.jsxs(uP, {
                children: [e.jsx(pP, {
                    children: t.number
                }), e.jsxs(pP, {
                    children: [e.jsx(rP, {
                        s: 14,
                        light: !0,
                        children: t.tenant.name ?? "---"
                    }), e.jsx(rP, {
                        s: 12,
                        light: !0,
                        gray: !0,
                        dir: "ltr",
                        children: t.tenant.phone
                    })]
                }), e.jsx(pP, {
                    children: pZ(t.unitName, 15)
                }), e.jsx(pP, {
                    children: pZ(t.buildingName)
                }), e.jsx(pP, {
                    children: pZ(t.communityName)
                }), e.jsx(pP, {
                    children: e.jsx(ve, {
                        label: t?.statusName?.toLowerCase() ?? "---",
                        sx: {
                            backgroundColor: J5.bg[t.status],
                            color: J5.text[t.status],
                            borderRadius: "32px",
                            textTransform: "capitalize"
                        }
                    })
                }), f ? e.jsx(pP, {
                    children: t.lastModified ? tR(t.lastModified).format("YYYY/MM/DD") : "---"
                }) : e.jsx(pP, {
                    children: d6(t.daysRemaining) ?? "---"
                }), e.jsx(pP, {
                    children: e.jsx(l, {
                        onClick: () => y(`/leasing/details/${t.id}`),
                        children: g("common.viewDetails")
                    })
                })]
            }, t.id))
        })
    })
}

function d7() {
    const {
        search: t,
        page: n,
        handleFilter: r,
        handleSearch: a,
        handleSort: i,
        sort: o,
        filter: s,
        setPage: l,
        leases: d,
        isLeasesLoading: c
    } = X8(), {
        t: u
    } = Gn();
    return e.jsxs(e.Fragment, {
        children: [e.jsx(rP, {
            mb: "28px",
            s: "36",
            children: u("breadcrumb.archive")
        }), e.jsx(IQ, {}), e.jsx(Ne, {
            sx: {
                mt: "28px",
                boxShadow: "0px 2px 4px 0px #0426520F",
                "& .MuiContainer-root": {
                    padding: 0
                },
                "& .MuiPaper-root": {
                    boxShadow: "none"
                }
            },
            children: e.jsx(l7, {
                leases: d,
                search: t,
                filterValues: $8,
                sortValues: G8,
                page: n,
                isLoading: c,
                setSearch: a,
                setFilter: r,
                setSort: i,
                setPage: l,
                pagesCount: d?.pagesCount,
                selectedFilters: s,
                sort: o,
                isArchive: !0
            })
        })]
    })
}
var c7 = (e => (e[e.Review_Records = 0] = "Review_Records", e[e.Security_Deposit = 1] = "Security_Deposit", e[e.Review = 2] = "Review", e))(c7 || {});
const u7 = e => [{
        title: e("dashboard.moveOut.reviewRecords"),
        description: e("dashboard.moveOut.reviewRecordsSub"),
        value: 0
    }, {
        title: e("dashboard.moveOut.securityDeposit"),
        description: e("dashboard.moveOut.securityDepositSub"),
        value: 1
    }, {
        title: e("dashboard.moveOut.review"),
        description: e("dashboard.moveOut.reviewSub"),
        value: 2
    }],
    p7 = e => v1().shape({
        end_at: d1().required(e("leaseForm.endDateRequired")),
        deductions: x1().of(v1().shape({
            deduction_amount: o1().transform((e, t) => "" === t ? null : e).nullable().required(e("leaseForm.pleaseAddDeductionAmount")).typeError(e("leaseForm.amountMustBeANumber")).min(1, e("leaseForm.amountMustBeGreaterThanZero")).test("two-decimal-places", e("leaseForm.onlyTwoDecimalPlacesAllowed"), e => {
                if (e) {
                    return (e?.toString()?.split(".")?.[1] || []).length <= 2
                }
                return !0
            }),
            deduction_description: a1().required(e("leaseForm.descriptionIsRequired")).max(1e3, e("leaseForm.descriptionMaxLength"))
        }))
    });

function h7(e = !1) {
    const {
        id: t
    } = qt(), {
        t: n
    } = Gn(), r = Ys(), a = Ft(), {
        data: i
    } = tl([rH, t], async () => await t6(Number(t)), {
        useErrorBoundary: !1
    }), o = i?.contract?.endDate && new Date(i.contract.endDate).getTime() > Date.now() ? new Date : new Date(i?.contract?.endDate || Date.now()), s = bf({
        defaultValues: {
            end_at: e ? void 0 : new Date
        },
        resolver: L1(p7(n))
    }), {
        mutate: l,
        isLoading: d
    } = nl({
        mutationFn: t => e ? (async e => {
            try {
                await co("/api-management/rf/leases/change-status/move-out", e)
            } catch (t) {
                throw t
            }
        })(t) : (async e => {
            try {
                await co("/api-management/rf/leases/change-status/terminate", e)
            } catch (t) {
                throw t
            }
        })(t),
        onSuccess: () => {
            r.invalidateQueries([rH, t]), a(`/leasing/details/${t}`)
        },
        onError: e => {
            Lo(e, {}, !0)
        }
    });
    return {
        lease: i,
        terminate: l,
        isTerminating: d,
        form: s,
        onSubmit: () => {
            const e = s.getValues();
            l({
                ...e,
                end_at: tR(e?.end_at).format("YYYY-MM-DD"),
                rf_lease_id: t
            })
        },
        securityDepositAmount: i?.deposit?.amount || 0,
        maxDate: e ? o : new Date
    }
}
const m7 = ({
        step: t,
        steps: n
    }) => {
        const {
            t: r
        } = Gn(), a = n?.find(e => +e.value === +t);
        return e.jsxs(cP, {
            column: !0,
            gap: "36px",
            id: mi,
            children: [e.jsx(cP, {
                children: e.jsx(IQ, {})
            }), e.jsx(f7, {
                steps: n,
                step: t
            }), e.jsxs(cP, {
                column: !0,
                ycenter: !0,
                sx: {
                    alignSelf: "center"
                },
                fullWidth: !0,
                children: [e.jsx(rP, {
                    s: 36,
                    color: "#232425",
                    children: a?.title
                }), t !== n?.length - 1 ? e.jsx(rP, {
                    s: 14,
                    light: !0,
                    color: "#232425",
                    textAlign: "center",
                    width: "48%",
                    children: a?.description
                }) : e.jsxs(cP, {
                    row: !0,
                    sx: {
                        backgroundColor: "#FCEDC7",
                        alignItems: "center",
                        my: "24px",
                        p: "12px",
                        gap: "12px",
                        borderRadius: "8px",
                        width: "100%"
                    },
                    children: [e.jsx(z8, {
                        color: "#FFC225"
                    }), e.jsxs(cP, {
                        children: [e.jsx(rP, {
                            s: "16",
                            children: r("common.warning")
                        }), e.jsx(rP, {
                            s: "16",
                            light: !0,
                            children: a?.description
                        })]
                    })]
                })]
            })]
        })
    },
    f7 = ({
        steps: t,
        step: n
    }) => e.jsx(cP, {
        sx: g7.stepperContainer,
        children: e.jsx(kt, {
            activeStep: n,
            children: t.map(({
                title: t
            }) => e.jsx(Tt, {
                children: e.jsx(jt, {
                    children: t
                })
            }, t))
        })
    }),
    g7 = {
        stepperContainer: {
            width: "75%",
            fontSize: "12px",
            mx: "auto",
            "& .MuiStepLabel-label.Mui-completed": {
                color: e => e?.palette?.primary?.main
            },
            "& .MuiStepLabel-label.Mui-active": {
                color: "#004256"
            },
            "& .MuiStepLabel-label.Mui-disabled": {
                color: "#B6B6B6"
            },
            "& .MuiSvgIcon-root.Mui-completed": {
                color: e => e?.palette?.primary?.main
            },
            "& .MuiSvgIcon-root.Mui-active": {
                color: "#EBF0F1"
            },
            "& .Mui-disabled .MuiSvgIcon-root": {
                color: "#fff",
                border: "2px solid #E3E3E3",
                borderRadius: "50%"
            },
            "& .Mui-disabled .MuiSvgIcon-root .MuiStepIcon-text": {
                fill: "#000000",
                fontWeight: "700",
                fontSize: "12px"
            },
            "& .MuiSvgIcon-root.Mui-active .MuiStepIcon-text": {
                fill: "#000000",
                fontWeight: "700",
                fontSize: "11px"
            },
            "& .MuiStepConnector-line": {
                border: "1px solid #E3E3E3"
            },
            "& .MuiStepConnector-root": {
                left: "calc(-50% + 14px)",
                right: "calc(50% + 14px)"
            }
        }
    },
    y7 = "data:image/svg+xml,%3csvg%20width='24'%20height='24'%20viewBox='0%200%2024%2024'%20fill='none'%20xmlns='http://www.w3.org/2000/svg'%3e%3cg%20clip-path='url(%23clip0_26362_25021)'%3e%3cpath%20d='M12.1727%2012.0001L9.34375%209.17208L10.7577%207.75708L15.0007%2012.0001L10.7577%2016.2431L9.34375%2014.8281L12.1727%2012.0001Z'%20fill='%23232425'/%3e%3c/g%3e%3cdefs%3e%3cclipPath%20id='clip0_26362_25021'%3e%3crect%20width='24'%20height='24'%20fill='white'/%3e%3c/clipPath%3e%3c/defs%3e%3c/svg%3e",
    v7 = ({
        title: t,
        subtitle: n,
        icon: r,
        onClick: a,
        iconComponent: i
    }) => {
        const {
            i18n: o
        } = Gn(), s = "rtl" === o.dir();
        return e.jsxs(cP, {
            row: !0,
            xbetween: !0,
            ycenter: !0,
            onClick: a,
            sx: {
                padding: "24px 16px ",
                border: "1px solid #E3E3E3",
                borderRadius: "16px",
                cursor: "pointer",
                backgroundColor: "white",
                "&:hover": {
                    backgroundColor: "#f0f0f045"
                }
            },
            children: [e.jsx(cP, {
                children: e.jsxs(cP, {
                    row: !0,
                    ycenter: !0,
                    sx: {
                        gap: "16px"
                    },
                    children: [!!i && i, !!r && e.jsx(cP, {
                        component: "img",
                        src: r,
                        sx: {
                            width: "32px",
                            height: "32px"
                        }
                    }), e.jsxs(cP, {
                        column: !0,
                        children: [e.jsx(rP, {
                            s: 24,
                            children: t
                        }), e.jsx(rP, {
                            s: 14,
                            gray: !0,
                            light: !0,
                            children: n
                        })]
                    })]
                })
            }), e.jsx(cP, {
                sx: {
                    pr: s ? 0 : 5,
                    pl: s ? 5 : 0,
                    transform: s ? "scale(-1)" : null
                },
                component: "img",
                src: y7,
                alt: "arrow-drop-right-line"
            })]
        })
    },
    _7 = "data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAACAAAAAgCAYAAABzenr0AAAACXBIWXMAAAsTAAALEwEAmpwYAAAAAXNSR0IArs4c6QAAAARnQU1BAACxjwv8YQUAAAFNSURBVHgB7ZffTcMwEMY/X8N7R6AbsEGzAabQ9zABbEKZgPYVtSEb0BFgg4yQAVofF7eqkih/LSqE6t+L7bPj+3y+RBfg0lF9F2qtx3QFDUeUQbbf4ytJkrRoD7oetI4Degb4CYwxHGE5qhohle6kl4CSY7Cz4yJK4bpqCwY4zgx4MSJ7ikbYqKVtwVsi2L4xKpIAhHXrSwL0XN8Qq886x9jhVe4vQwezh7tl3ipGunlPVtY216G9gy4BBMipT/c8yLErjTlgdjw5p+NOATTCy+y++a0zwDbZnEIciWFanGeoUJ5/O4xUiKECJGUjtEDMebM6DqfV9YeMP9q4ZZ/SiOkbZ8TmVIVSBOJ1nC9YtG0iWc5t8/H6o/fXNYfwx3gBXoAX4AV4AV6AF9D5Y9KIVEBSG0S2z3DGJQK/WqgOjoBhfhTVt7VzUqjiv/EDnw1srwemH5QAAAAASUVORK5CYII=";

function x7({
    id: t
}) {
    const {
        t: n
    } = Gn(), [r, a] = Dt.useState(1), [i, o] = Dt.useState(""), [s, l] = Dt.useState(""), {
        data: c,
        isLoading: u
    } = tl([`${yF}-${_F}`, {
        id: t,
        page: r,
        search: s,
        filter: i
    }], async () => await K$(t, r, s, i, "lease")), p = u;
    return p ? e.jsx(cP, {
        center: !0,
        sx: {
            height: "100%",
            my: 50
        },
        children: e.jsx(d, {})
    }) : e.jsxs(e.Fragment, {
        children: [e.jsx(hp, {
            s: 24,
            bold: !0,
            color: "#232425",
            my: "28px",
            children: n("contacts.Transactions")
        }), e.jsx(b7, {
            isLoading: p,
            data: c?.data?.list,
            filters: e.jsxs(e.Fragment, {
                children: [e.jsx(RQ, {
                    search: s,
                    handleSearch: e => {
                        a(1), l(e), o(t)
                    }
                }), e.jsx(hJ, {
                    filtering: !0,
                    sorting: !1,
                    filterValues: S7,
                    handleFilter: e => {
                        a(1), l(s), o(e)
                    }
                })]
            }),
            pagination: e.jsx(HQ, {
                page: r,
                count: c?.data?.paginator?.last_page,
                handler: e => a(e)
            })
        })]
    })
}
const b7 = ({
        data: t,
        filters: n,
        pagination: r,
        isLoading: a
    }) => {
        const {
            t: i
        } = Gn();
        return e.jsx(e.Fragment, {
            children: e.jsx(o7, {
                data: t,
                RenderTable: e.jsx(E5, {
                    isLoading: a,
                    filters: n,
                    isEmpty: !t?.length,
                    pagination: r,
                    headerData: w7(i),
                    children: e.jsx(M7, {
                        data: t
                    })
                })
            })
        })
    },
    w7 = e => [e("leasing.leaseNumber"), e("dashboard.moveOut.transactionType"), e("dashboard.moveOut.amt"), e("dashboard.moveOut.paidDate"), e("accounting.remaining"), e("headers.status"), ""],
    C7 = ({
        status: t,
        color: n,
        bgColor: r
    }) => e.jsx(cP, {
        center: !0,
        sx: {
            alignSelf: "center",
            width: "fit-content",
            padding: "4px 12px",
            borderRadius: "32px",
            backgroundColor: r
        },
        children: e.jsx(hp, {
            light: !0,
            s: 16,
            color: n,
            children: t
        })
    }),
    M7 = ({
        data: t
    }) => {
        const {
            t: n,
            i18n: r
        } = Gn();
        r.dir();
        const a = Ft(),
            i = t => {
                switch (t) {
                    case "paid":
                        return e.jsx(C7, {
                            status: n("paid"),
                            color: "0A9458",
                            bgColor: "#EDFAF4"
                        });
                    case "overdue":
                        return e.jsx(C7, {
                            status: n("overdue"),
                            color: "812222",
                            bgColor: "#FFE5E5"
                        });
                    default:
                        return e.jsx(C7, {
                            status: n("outstanding"),
                            color: "8A6A16",
                            bgColor: "#FCEDC7"
                        })
                }
            };
        return e.jsx(e.Fragment, {
            children: !!t?.length && t?.map(t => e.jsxs(uP, {
                children: [e.jsx(pP, {
                    sx: {
                        fontWeight: "bold"
                    },
                    children: t?.lease_number || "-"
                }), e.jsx(pP, {
                    sx: {
                        fontWeight: "bold"
                    },
                    children: t?.category?.name || "-"
                }), e.jsx(pP, {
                    sx: {
                        fontWeight: "bold"
                    },
                    children: e.jsx(hp, {
                        variant: "caption",
                        bold: !0,
                        currency: !0,
                        children: t?.amount_fmt
                    })
                }), e.jsx(pP, {
                    children: t?.due_on
                }), e.jsx(pP, {
                    sx: {
                        fontWeight: "bold"
                    },
                    children: e.jsx(hp, {
                        variant: "caption",
                        bold: !0,
                        currency: !0,
                        children: t?.left_fmt
                    })
                }), e.jsx(pP, {
                    children: i(t?.type?.name)
                }), e.jsx(pP, {
                    children: e.jsx(dP, {
                        sx: {
                            py: 2
                        },
                        onClick: e => {
                            e.stopPropagation(), a(`/transactions/${t?.id}`)
                        },
                        children: e.jsx(hp, {
                            variant: "caption",
                            sx: {
                                color: "#008EA5"
                            },
                            children: n("announcements.view details")
                        })
                    })
                })]
            }, t?.id))
        })
    },
    S7 = {
        1: {
            title: "paid",
            color: "#0A9458",
            background: "#EDFAF4"
        },
        3: {
            title: "overdue",
            color: "#812222",
            background: "#FFE5E5"
        },
        2: {
            title: "outstanding",
            color: "#8A6A16",
            background: "#FCEDC7"
        }
    },
    L7 = ({
        leaseId: t
    }) => {
        const {
            t: n
        } = Gn(), r = Ft();
        return e.jsxs(cP, {
            sx: {
                width: "100%"
            },
            children: [e.jsxs(cP, {
                my: "24px",
                column: !0,
                gap: "16px",
                children: [e.jsx(v7, {
                    title: n("dashboard.serviceRequests"),
                    subtitle: n("serviceRequestLeaseRequests"),
                    icon: _7,
                    onClick: () => r("serviceRequests", {
                        state: {
                            leaseId: t
                        }
                    })
                }), e.jsx(v7, {
                    title: n("serviceRequest.Visitor Access"),
                    subtitle: n("visitorAccessLeaseRequests"),
                    iconComponent: e.jsx(WN.VisitorAccessIcon, {
                        sx: {
                            width: "32px",
                            height: "32px",
                            color: "text.secondary"
                        }
                    }),
                    onClick: () => r("visitor-access")
                })]
            }), t && e.jsx(w6, {
                children: e.jsx(x7, {
                    id: t?.toString()
                })
            })]
        })
    },
    k7 = ({
        isMoveout: t
    }) => {
        const {
            lease: n,
            maxDate: r
        } = h7(t), {
            t: a
        } = Gn(), i = u7(a), {
            control: o,
            formState: {
                errors: s
            }
        } = Lm(), l = n?.contract?.creationDate;
        return e.jsxs(cP, {
            sx: {
                width: "100%"
            },
            children: [e.jsxs(w6, {
                children: [e.jsx(m7, {
                    step: 0,
                    steps: i
                }), e.jsx(rP, {
                    s: 16,
                    color: "#232425",
                    children: a(t ? "dashboard.moveOut.moveOutDate" : "dashboard.moveOut.terminateLeaseDate")
                }), e.jsx(cP, {
                    my: "16px",
                    width: "370px",
                    children: e.jsx(I2, {
                        name: "end_at",
                        label: a(t ? "dashboard.moveOut.selectMoveOutDate" : "dashboard.moveOut.selectTerminateLeaseDate"),
                        placeholderText: `${a("leaseForm.selectDate")}`,
                        minDate: new Date(l),
                        maxDate: r,
                        errors: s,
                        control: o,
                        readOnly: !0
                    })
                })]
            }), e.jsx(L7, {
                leaseId: n?.id
            })]
        })
    },
    T7 = ({
        step: t,
        setStep: n,
        isMoveout: r = !1,
        children: a
    }) => {
        const {
            form: i,
            onSubmit: o,
            isTerminating: s,
            securityDepositAmount: l
        } = h7(r), [d, c] = Dt.useState(!1), {
            t: u
        } = Gn();
        return e.jsxs(sP, {
            maxWidth: "xl",
            component: "div",
            children: [e.jsx(km, {
                ...i,
                children: e.jsx(cP, {
                    component: "form",
                    sx: {
                        width: "100%"
                    },
                    children: e.jsxs(e.Fragment, {
                        children: [a, e.jsxs(cP, {
                            row: !0,
                            gap: "18px",
                            mt: "26px",
                            children: [t !== c7.Review_Records && e.jsx(dP, {
                                type: "button",
                                variant: "outlined",
                                sx: {
                                    px: "66px",
                                    py: "10px"
                                },
                                onClick: () => {
                                    n(e => e - 1)
                                },
                                children: u("leaseForm.previous")
                            }), t !== c7.Review && e.jsx(dP, {
                                type: "button",
                                variant: "contained",
                                sx: {
                                    px: "66px",
                                    py: "10px"
                                },
                                onClick: async () => {
                                    if (t === c7.Review_Records) {
                                        if (!(await i.trigger("end_at"))) return
                                    }
                                    if (t === c7.Security_Deposit) {
                                        if (!(await i.trigger("deductions"))) return;
                                        const e = i.getValues("deductions")?.reduce((e, t) => e + +t.deduction_amount, 0);
                                        if (e > l) return void Zi.error(u("leaseForm.deductionLessThanSecurityDeposit"))
                                    }
                                    n(e => e + 1)
                                },
                                children: u("common.next")
                            }), t === c7.Review && e.jsx(dP, {
                                disabled: s,
                                onClick: () => c(!0),
                                variant: "contained",
                                color: "error",
                                sx: {
                                    px: "66px",
                                    py: "10px"
                                },
                                children: u(s ? "loading" : r ? "dashboard.quickAccess.moveout_tenant" : "Terminate leases")
                            })]
                        })]
                    })
                })
            }), e.jsx(t5, {
                isOpen: d,
                title: u(r ? "leasing.confirmMoveoutTitle" : "leasing.confirmTerminateTitle"),
                handleClose: () => c(!1),
                body: u(r ? "leasing.confirmMoveoutBody" : "leasing.confirmTerminateBody"),
                hidetoast: !0,
                deleteFunc: () => {
                    o()
                },
                queryKey: []
            })]
        })
    },
    j7 = () => {
        const {
            securityDepositAmount: t
        } = h7(), {
            t: n
        } = Gn(), r = u7(n), {
            setValue: a,
            control: i,
            formState: {
                errors: o
            },
            watch: s
        } = Lm(), l = e => {
            a("deductions", e ? [{
                amount: 0,
                description: ""
            }] : [])
        }, d = s("deductions"), c = !!d?.length;
        return e.jsxs(cP, {
            sx: {
                width: "100%"
            },
            children: [e.jsx(w6, {
                children: e.jsx(m7, {
                    step: 1,
                    steps: r
                })
            }), e.jsxs(w6, {
                sx: {
                    my: "24px"
                },
                children: [e.jsx(hp, {
                    s: 24,
                    gray: !0,
                    textAlign: "center",
                    children: n("dashboard.moveOut.securityDeposit")
                }), e.jsx(hp, {
                    s: 36,
                    currency: !0,
                    color: "#000",
                    textAlign: "center",
                    children: t
                })]
            }), e.jsxs(w6, {
                sx: {
                    my: "24px"
                },
                children: [e.jsxs(cP, {
                    row: !0,
                    xbetween: !0,
                    ycenter: !0,
                    children: [e.jsx(hp, {
                        s: 24,
                        children: n("dashboard.moveOut.deductions")
                    }), c ? e.jsx(dP, {
                        variant: "outlined",
                        color: "error",
                        onClick: () => l(!1),
                        children: n("common.remove")
                    }) : e.jsx(dP, {
                        startIcon: e.jsx(jf, {}),
                        onClick: () => l(!0),
                        children: n("common.add")
                    })]
                }), d?.map((t, r) => e.jsxs(cP, {
                    sx: {
                        my: "24px",
                        border: "1px solid #E3E3E3",
                        borderRadius: "16px",
                        padding: "24px",
                        position: "relative"
                    },
                    children: [e.jsx(cP, {
                        sx: {
                            position: "absolute",
                            right: "24px",
                            top: "24px"
                        },
                        children: e.jsx(w, {
                            onClick: () => {
                                (e => {
                                    const t = d?.filter((t, n) => n !== e);
                                    a("deductions", t)
                                })(r)
                            },
                            children: e.jsx(l8, {
                                sx: {
                                    width: "24px",
                                    height: "24px"
                                }
                            })
                        })
                    }), e.jsx(o$, {
                        errors: o,
                        type: "number",
                        name: `deductions[${r}].deduction_amount`,
                        control: i,
                        placeholder: `${n("dashboard.moveOut.amtPH")}`,
                        label: `${n("dashboard.moveOut.deduction")}`,
                        sx: {
                            backgroundColor: "#FFFFFF",
                            width: "400px"
                        }
                    }), e.jsx(o$, {
                        errors: o,
                        name: `deductions[${r}].deduction_description`,
                        multiline: !0,
                        control: i,
                        placeholder: `${n("dashboard.moveOut.descriptionPH")}`,
                        rows: 4,
                        label: `${n("dashboard.moveOut.description")}`,
                        sx: {
                            backgroundColor: "#FFFFFF"
                        }
                    })]
                }, r)), !!d?.length && d?.length < 5 && e.jsx(dP, {
                    variant: "text",
                    startIcon: e.jsx(jf, {}),
                    onClick: () => {
                        a("deductions", [...s("deductions"), {
                            amount: null,
                            description: ""
                        }])
                    },
                    children: n("requestsCategories.Add More")
                })]
            })]
        })
    },
    E7 = ({
        isMoveout: t
    }) => {
        const {
            lease: n,
            securityDepositAmount: r
        } = h7(), {
            t: a,
            i18n: {
                language: i
            }
        } = Gn(), o = u7(a), l = (e => [{
            title: e("Review Records"),
            description: e("Please review the tenant records to close pending items."),
            value: 0
        }, {
            title: e("Security Deposit"),
            description: e("Please review the amount to be returned and add any deductions"),
            value: 1
        }, {
            title: e("Review"),
            description: e("Please review the information related to the Lease. Once Move out the action cannot be undone."),
            value: 2
        }])(a), d = s(), c = M8(n, a), u = ce(d.breakpoints.up("xl")), p = n?.total_unpaid_amount, h = n?.unpaid_transactions_count, {
            watch: m
        } = Lm(), f = m("deductions"), g = f?.reduce((e, t) => e + Number(t?.deduction_amount || 0), 0) || 0;
        return e.jsxs(cP, {
            sx: {
                width: "100%"
            },
            children: [e.jsx(w6, {
                sx: {
                    mb: "24px"
                },
                children: e.jsx(m7, {
                    step: c7.Review,
                    steps: t ? l : o
                })
            }), e.jsxs(e8, {
                title: a("contacts.Tenant Details"),
                cols: u ? 7 : 6,
                children: [n?.tenant?.name || n?.tenant?.name_en || n?.tenant?.name_ar ? e.jsx(k8, {
                    tenant: n?.tenant,
                    sx: {
                        mr: "24px"
                    },
                    isReview: !0
                }) : null, c?.tenant?.filter(e => null != e)?.map(e => Dt.createElement(L8, {