            isOpen: g,
            onClose: () => y(!1),
            onConfirm: w,
            isLoading: S
        }), e.jsx(yte, {
            isOpen: v.isOpen,
            onClose: _,
            type: v.type,
            title: v.title,
            message: v.message
        })]
    })
}
const _te = {
        container: {
            padding: "48px",
            minHeight: "100vh",
            backgroundColor: "#F9FAFB"
        },
        loadingContainer: {
            display: "flex",
            justifyContent: "center",
            alignItems: "center",
            minHeight: "100vh"
        },
        header: {
            pt: "12px",
            display: "flex",
            justifyContent: "space-between",
            alignItems: "center",
            flexWrap: "wrap",
            gap: 2
        },
        headerLeft: {
            display: "flex",
            alignItems: "center",
            gap: 2
        },
        headerRight: {
            display: "flex",
            gap: 6
        },
        pageTitle: {
            fontWeight: 700,
            fontSize: "32px",
            color: "#111827",
            mt: 2
        },
        deleteButton: {
            color: "#EF4444",
            borderColor: "#EF4444",
            "&:hover": {
                borderColor: "#DC2626",
                backgroundColor: "rgba(239, 68, 68, 0.04)"
            },
            px: "20px",
            py: "8px",
            fontWeight: 600
        },
        convertButton: {
            minWidth: 180,
            px: "20px",
            py: "8px",
            fontWeight: 600
        }
    },
    xte = ({
        status: t,
        handleTryAgain: n,
        excelErrors: r,
        apiErrorMessage: a
    }) => {
        const {
            t: i
        } = Gn(), o = Ft(), s = () => {
            n()
        };
        return "failed" === t ? e.jsx(lh, {
            isOpen: "failed" === t,
            variant: "error",
            content: {
                title: i("leads.uploadFailure"),
                body: a || i("leads.uploadFailureMessage")
            },
            closeBtnText: i("leads.close"),
            primaryButton: r?.length ? {
                title: i("leads.viewIssues"),
                handleClick: () => {
                    o("/marketplace/customers/upload-leads/errors", {
                        state: {
                            excelErrors: r
                        }
                    })
                },
                variant: "contained"
            } : null,
            onDialogClose: s
        }) : "success" === t ? e.jsx(lh, {
            isOpen: "success" === t,
            variant: "success",
            content: {
                title: i("leads.uploadSuccessful"),
                body: i("leads.uploadSuccessfulMessage")
            },
            closeBtnText: i("leads.uploadMoreFiles"),
            primaryButton: {
                title: i("leads.done"),
                handleClick: () => {
                    o("/marketplace/customers")
                },
                variant: "contained"
            },
            onDialogClose: s
        }) : null
    },
    bte = () => {
        const {
            t: t
        } = Gn(), {
            status: n,
            handleSubmit: r,
            form: a,
            watch: i,
            isLoading: o,
            handleTryAgain: s,
            excelErrors: l,
            apiErrorMessage: d,
            onSubmit: c
        } = (() => {
            const [e, t] = Dt.useState(!1), [n, r] = Dt.useState(null), [a, i] = Dt.useState(null), [o, s] = Dt.useState(null), l = Ys(), d = bf({
                defaultValues: {}
            }), {
                register: c,
                handleSubmit: u,
                watch: p,
                formState: {
                    errors: h
                }
            } = d, m = async e => {
                const t = new FormData,
                    n = e.file;
                t.append("file", n[0]);
                try {
                    await bo.post("/rf/excel-sheets/leads", t, {
                        headers: {
                            "Content-Type": "multipart/form-data"
                        }
                    })
                } catch (r) {
                    throw r
                }
            };
            return {
                register: c,
                handleSubmit: u,
                errors: h,
                onSubmit: async e => {
                    const n = e || d.getValues();
                    t(!0);
                    try {
                        await m(n), await l.invalidateQueries({
                            queryKey: [jH]
                        }), r("success"), t(!1)
                    } catch (a) {
                        i(a?.response?.data?.errors || null), s(a?.response?.data?.message || null), t(!1), r("failed"), Lo(a, void 0, !0)
                    }
                },
                form: d,
                watch: p,
                status: n,
                setStatus: r,
                handleTryAgain: () => {
                    r(null), d.resetField("file")
                },
                isLoading: e,
                apiErrorMessage: o,
                excelErrors: a
            }
        })(), u = i("file");
        return e.jsxs(ap, {
            mt: "28px",
            column: !0,
            sx: {
                width: "100%",
                px: "48px",
                pb: "48px"
            },
            children: [e.jsxs(ite, {
                children: [e.jsx(IQ, {}), e.jsx(hp, {
                    variant: "h4",
                    mt: "32px",
                    mb: "4px",
                    children: t("leads.importYourLeads")
                }), e.jsx(hp, {
                    variant: "body",
                    children: t("leads.importDescription")
                })]
            }), e.jsx(ite, {
                children: e.jsxs(ap, {
                    row: !0,
                    xbetween: !0,
                    ycenter: !0,
                    children: [e.jsxs(ap, {
                        children: [e.jsx(hp, {
                            variant: "h5",
                            mb: "4px",
                            children: t("leads.step1DownloadTemplate")
                        }), e.jsx(hp, {
                            variant: "body",
                            color: "text.secondary",
                            children: t("leads.step1Description")
                        })]
                    }), e.jsx(wp, {
                        startIcon: e.jsx(WN.DownloadIcon, {}),
                        onClick: () => (async () => {
                            try {
                                const e = (await lo("/api/general/static-files/download_lead_excel")).data.url;
                                window.open(e, "_blank")
                            } catch (e) {
                                throw e
                            }
                        })(),
                        variant: "contained",
                        sx: {
                            minWidth: "260px"
                        },
                        children: t("leads.downloadLeadsTemplate")
                    })]
                })
            }), e.jsxs(ite, {
                children: [e.jsx(hp, {
                    variant: "h5",
                    mb: "24px",
                    children: t("leads.step2UploadCompletedFile")
                }), e.jsxs("form", {
                    onSubmit: r(async () => {
                        const e = a.getValues();
                        return c(e)
                    }),
                    autoComplete: "off",
                    children: [e.jsx(E4, {
                        showThumbs: !1,
                        name: "file",
                        label: "",
                        maxFileSize: lp,
                        dropzoneText: "",
                        defaultPreview: !0,
                        dropZoneAreaComponent: () => e.jsx(yE, {
                            dropZoneText: t("leads.uploadCompletedExcelFile"),
                            allowedFormats: dp.excel,
                            maxFiles: 1,
                            maxFileSize: lp
                        }),
                        dropzoneArea: !0,
                        acceptedFiles: dp.excel,
                        filesLimit: 1,
                        customErrors: {
                            format: t("error.fileformat")
                        },
                        form: a,
                        errors: a.formState.errors,
                        rules: {
                            required: !0
                        },
                        displayLogo: V4
                    }), u && e.jsx(k6, {
                        excelFile: u,
                        handleRemove: () => a.resetField("file"),
                        isLoading: o
                    }), e.jsx(ap, {
                        sx: {
                            mt: 18,
                            textAlign: "left"
                        },
                        children: e.jsx(wp, {
                            isLoading: o,
                            disabled: !u || o,
                            size: "large",
                            fullWidth: !1,
                            type: "submit",
                            variant: "contained",
                            sx: {
                                px: 40
                            },
                            children: t(o ? "leads.uploadingFile" : "leads.uploadAndSave")
                        })
                    })]
                }), e.jsx(xte, {
                    status: n,
                    handleTryAgain: s,
                    excelErrors: l,
                    apiErrorMessage: d
                })]
            })]
        })
    },
    wte = () => {
        const {
            t: t
        } = Gn(), n = Ht(), r = Ft(), a = n?.state?.excelErrors || [];
        return e.jsx(Ae, {
            maxWidth: "lg",
            sx: {
                position: "absolute",
                px: "48px",
                pb: "48px",
                pt: "18px"
            },
            children: e.jsxs(cP, {
                children: [e.jsxs(cP, {
                    sx: {
                        mb: "24px"
                    },
                    children: [e.jsx(IQ, {
                        handleBackAction: () => r("/marketplace/customers/upload-leads")
                    }), e.jsx(o, {
                        variant: "h4",
                        sx: {
                            my: "24px"
                        },
                        children: t("leads.reviewExcelUploadFile")
                    }), e.jsx(F5, {
                        title: t("leads.pendingIssues"),
                        body: t("leads.pendingIssuesMessage"),
                        bg: "#FFE5E5",
                        iconColor: "#FF4242"
                    })]
                }), e.jsx(E5, {
                    isLoading: !1,
                    isEmpty: !1,
                    headerData: [t("leads.errorNumber"), t("leads.errorMessage")],
                    children: a.map((t, n) => e.jsxs(uP, {
                        children: [e.jsx(pP, {
                            component: "th",
                            scope: "row",
                            sx: {
                                fontWeight: "bold",
                                width: "0px"
                            },
                            children: n + 1
                        }), e.jsx(pP, {
                            component: "th",
                            scope: "row",
                            sx: {
                                fontWeight: "bold"
                            },
                            children: t
                        })]
                    }, n))
                })]
            })
        })
    };

function Cte() {
    const {
        state: {
            search: e,
            page: t
        },
        setSearch: n,
        setPage: r
    } = AQ({
        enableFilter: !1,
        enableSort: !1,
        shouldRerenderOnChange: !1
    }), {
        data: a,
        isLoading: i
    } = tl([SH, e, t], async () => await L9({
        query: e,
        page: t
    }), {
        useErrorBoundary: !1
    }), [o, s] = Dt.useState(null), l = Ys(), {
        t: d
    } = Gn(), {
        mutate: c,
        isLoading: u
    } = nl({
        mutationFn: async e => await (async e => {
            try {
                await co(`/api-management/marketplace/admin/communities/unlist/${e}`)
            } catch (t) {
                throw t
            }
        })(e),
        onSuccess: () => {
            s(null), l.invalidateQueries([SH]), Zi.success(d("marketplace.unlisted"))
        }
    });
    return {
        list: a?.list,
        isLoading: i,
        metadata: a?.metadata,
        search: e,
        setSearch: n,
        page: t,
        setPage: r,
        unList: c,
        isUnListing: u,
        communityToBeUnlisted: o,
        setCommunityToBeUnlisted: s
    }
}
const Mte = {
    [e9.AVAILABLE]: {
        bg: "#EDFAF4",
        txt: "#0A9458"
    },
    [e9.COMING_SOON]: {
        bg: "#FCEDC7",
        txt: "#8A6A16"
    },
    [e9.SOLD]: {
        bg: "#EBF0F1",
        txt: "#002A37"
    },
    [e9.RENTED]: {
        bg: "#EBF6F8",
        txt: "#00697A"
    }
};

function Ste({
    data: t,
    unList: n,
    showInterests: r,
    isUnlisting: a
}) {
    const {
        t: i
    } = Gn(), o = t?.interests > 0, [s, l] = Dt.useState(null), d = 1 === +t.is_off_plan_sale;
    e9.SOLD;
    const c = d && t.active_bookings_count > 0;
    return e.jsxs(uP, {
        children: [e.jsx(pP, {
            component: "td",
            scope: "row",
            children: e.jsx(rP, {
                s: "16",
                children: t?.name
            })
        }), e.jsx(pP, {
            component: "td",
            scope: "row",
            children: e.jsx(rP, {
                s: "16",
                light: !0,
                children: t?.city
            })
        }), e.jsx(pP, {
            component: "td",
            scope: "row",
            children: e.jsx(rP, {
                s: "16",
                light: !0,
                children: t?.district
            })
        }), e.jsx(pP, {
            component: "td",
            scope: "row",
            children: e.jsx(rP, {
                s: "16",
                light: !0,
                children: t?.units
            })
        }), e.jsx(pP, {
            component: "td",
            scope: "row",
            children: e.jsx(rP, {
                s: "16",
                light: !0,
                children: e.jsx(dP, {
                    variant: "text",
                    color: "primary",
                    disabled: !o,
                    sx: {
                        textDecoration: o ? "underline" : "none",
                        textDecorationColor: e => o ? e.palette.primary.main : "",
                        "&:hover": {
                            textDecoration: o ? "underline" : "none",
                            textDecorationColor: e => o ? e.palette.primary.main : ""
                        },
                        "&:disabled": {
                            color: "#232425",
                            fontWeight: "normal"
                        }
                    },
                    onClick: r,
                    children: t?.interests
                })
            })
        }), e.jsx(pP, {
            component: "td",
            scope: "row",
            children: e.jsx(rP, {
                s: "16",
                light: !0,
                textTransform: "capitalize",
                children: +t.is_off_plan_sale ? i("offPlanSales") : i(`sellingOptions.${t.list_for}`)
            })
        }), e.jsx(pP, {
            component: "td",
            scope: "row",
            children: e.jsx(ve, {
                label: t?.status?.name,
                sx: {
                    bgcolor: Mte[t?.status?.id]?.bg,
                    color: Mte[t?.status?.id]?.txt
                }
            })
        }), e.jsxs(pP, {
            component: "td",
            scope: "row",
            sx: {
                display: "flex",
                justifyContent: "space-between"
            },
            children: [e.jsx(dP, {
                variant: "text",
                color: "primary",
                component: Wt,
                to: `manage/${t?.id}?listFor=${t?.list_for}&communityName=${encodeURIComponent(t?.name)}&isOfPlan=${t?.is_off_plan_sale}`,
                children: i("common.manage")
            }), ni.can(qI.Update, $I.Listings) && e.jsx(Kp, {
                title: c ? i("marketplace.unlistDisabledTooltip") : "",
                variant: "primary",
                arrow: !0,
                children: e.jsx("span", {
                    children: e.jsx(dP, {
                        variant: "contained",
                        sx: {
                            bgcolor: "#FF424214",
                            color: "#FF4242",
                            width: "110px",
                            height: "40px",
                            "&:hover": {
                                bgcolor: "#FF4242",
                                color: "#fff"
                            }
                        },
                        onClick: () => {
                            c || (l(t?.id), n(t?.id))
                        },
                        disabled: a || c,
                        isLoading: a && s === t?.id,
                        children: i("marketplace.unlist")
                    })
                })
            })]
        })]
    })
}

function Lte({
    communityId: t,
    open: n,
    onClose: r
}) {
    const {
        data: a,
        isLoading: i
    } = tl(["MP_COMMUNITY_INTERESTS", t], () => (async e => {
        try {
            return t = await lo(`/api-management/marketplace/favorites/communities/${e}`), t?.data?.map(e => ({
                ...e,
                phone: e.phone_number
            }))
        } catch (n) {
            throw n
        }
        var t
    })(t), {
        useErrorBoundary: !1,
        enabled: !!t && n
    }), {
        t: o
    } = Gn();
    return e.jsxs(v, {
        onClose: r,
        open: n,
        fullWidth: !0,
        maxWidth: "xs",
        children: [e.jsx(TJ, {
            title: o("marketplace.customers"),
            handleClose: r
        }), i ? e.jsx(cP, {
            center: !0,
            py: "64px",
            children: e.jsx(d, {})
        }) : e.jsx(cP, {
            sx: {
                display: "flex",
                flexDirection: "column",
                alignItems: "flex-start",
                gap: "16px",
                p: "20px"
            },
            children: a?.map(t => e.jsxs(cP, {
                sx: {
                    display: "flex",
                    alignItems: "center",
                    gap: "4px"
                },
                children: [e.jsx(Vp, {
                    name: t.name,
                    sx: {
                        mr: 4
                    }
                }), e.jsxs(cP, {
                    children: [e.jsx(rP, {
                        s: "14",
                        children: t.name
                    }), e.jsx(rP, {
                        s: "14",
                        light: !0,
                        dir: "ltr",
                        textAlign: "left",
                        children: t.phone
                    })]
                })]
            }, t.id))
        })]
    })
}

function kte({
    fetcher: e,
    refetchKey: t,
    title: n,
    enabled: r,
    chosenProperties: a,
    queryKey: i
}) {
    const [o, s] = Dt.useState(a), [l, d] = Dt.useState(""), [c, u] = Dt.useState(""), p = Dt.useMemo(() => bh.debounce(e => {
        d(e)
    }, 1e3), []), {
        data: h,
        fetchNextPage: m,
        hasNextPage: f,
        isFetching: g,
        isLoading: y
    } = al({
        queryKey: [i ?? "PROPERTY_LIST", l, n, t],
        queryFn: ({
            pageParam: t = 1
        }) => e({
            page: t,
            search: l
        }),
        getNextPageParam: (e, t) => e?.length ? t?.length + 1 : void 0,
        enabled: r
    }), v = Dt.useRef(void 0), _ = Dt.useCallback(e => {
        y || (v.current && v.current.disconnect(), v.current = new IntersectionObserver(e => {
            e[0].isIntersecting && f && !g && m()
        }), e && v.current.observe(e))
    }, [m, f, g, y]), x = h?.pages?.reduce((e, t) => Array.isArray(e) && Array.isArray(t) ? [...e, ...t] : e, []);
    return {
        chosenProperties: o,
        setChosenProperty: s,
        list: x,
        isLoading: y,
        handleSearch: e => {
            u(e), p(e)
        },
        search: c,
        isFetching: g,
        lastElementRef: _
    }
}

function Tte({
    fetcher: e,
    title: t,
    isOpen: n,
    onClose: r
}) {
    const {
        list: a,
        isLoading: i,
        chosenProperties: o,
        setChosenProperty: s,
        isFetching: l,
        lastElementRef: d
    } = kte({
        fetcher: e,
        title: t,
        enabled: n,
        chosenProperties: [],
        queryKey: "MP_COMMUNITIES_NON_LISTED"
    }), c = Ys(), {
        t: u
    } = Gn(), {
        mutate: p,
        isLoading: h
    } = nl({
        mutationFn: async ({
            id: e,
            type: t,
            cash: n,
            bank: r
        }) => await (async (e, t, n, r) => {
            try {
                await co(`/api-management/marketplace/admin/communities/list/${e}`, {
                    is_buy: "sale" === t ? 1 : 0,
                    allow_cash_sale: n ? 1 : 0,
                    allow_bank_financing: r ? 1 : 0
                })
            } catch (a) {
                throw a
            }
        })(e, t, n, r),
        onSuccess: () => {
            c.invalidateQueries([SH]), Zi.success(u("marketplace.listed")), r(), s([])
        }
    });
    return {
        list: a,
        isListing: h,
        isLoading: i,
        chosenProperty: o?.[0],
        setChosenProperty: s,
        isFetching: l,
        lastElementRef: d,
        listCommunityMutation: p
    }
}

function jte({
    fetcher: t,
    title: n,
    isOpen: r,
    onClose: a,
    listingType: i
}) {
    const {
        list: o,
        isLoading: s,
        chosenProperty: l,
        setChosenProperty: c,
        isFetching: u,
        lastElementRef: p,
        listCommunityMutation: h,
        isListing: m
    } = Tte({
        fetcher: t,
        title: n,
        isOpen: r,
        onClose: a
    }), [f, g] = Dt.useState({
        cash: !0,
        bank: !1
    }), y = () => {
        a(), c([]), g({
            cash: !0,
            bank: !1
        })
    }, {
        t: x
    } = Gn();
    return e.jsxs(v, {
        onClose: y,
        open: r,
        fullWidth: !0,
        maxWidth: "sm",
        children: [e.jsxs(cP, {
            component: "header",
            sx: {
                display: "flex",
                justifyContent: "space-between",
                alignItems: "center",
                p: "2rem"
            },
            children: [e.jsx(rP, {
                sx: {
                    "&.MuiTypography-root": {
                        fontSize: "24px"
                    }
                },
                children: n
            }), e.jsx(w, {
                sx: {
                    color: "#000"
                },
                onClick: y,
                children: e.jsx(ph, {})
            })]
        }), e.jsxs(_, {
            sx: {
                p: "0 2rem"
            },
            children: [e.jsxs(cP, {
                sx: {
                    mb: "1.5rem"
                },
                children: [e.jsx(rP, {
                    variant: "body",
                    color: "text.secondary",
                    sx: {
                        mb: 1
                    },
                    children: x("availablePurchaseMethods")
                }), e.jsxs(cP, {
                    row: !0,
                    gap: "16px",
                    children: [e.jsx(A, {
                        control: e.jsx(S, {
                            checked: f.cash,
                            onChange: (e, t) => g(e => ({
                                ...e,
                                cash: t
                            })),
                            size: "small"
                        }),
                        label: x("cashPaymentTitle"),
                        sx: {
                            m: 0,
                            "& .MuiTypography-root": {
                                fontSize: 14,
                                fontWeight: 400
                            }
                        }
                    }), e.jsx(A, {
                        control: e.jsx(S, {
                            checked: f.bank,
                            onChange: (e, t) => g(e => ({
                                ...e,
                                bank: t
                            })),
                            size: "small"
                        }),
                        label: x("bankFinancing"),
                        sx: {
                            m: 0,
                            "& .MuiTypography-root": {
                                fontSize: 14,
                                fontWeight: 400
                            }
                        }
                    })]
                })]
            }), s ? e.jsx(cP, {
                center: !0,
                sx: {
                    minHeight: "200px"
                },
                children: e.jsx(d, {})
            }) : o?.length ? e.jsxs(e.Fragment, {
                children: [e.jsxs(cP, {
                    sx: {
                        maxHeight: "410px",
                        overflowY: "auto"
                    },
                    children: [o?.map(t => e.jsx("div", {
                        ref: p,
                        children: e.jsxs(cP, {
                            sx: {
                                display: "flex",
                                justifyContent: "space-between",
                                alignItems: "center",
                                margin: "1rem 0",
                                padding: "12px",
                                cursor: "pointer",
                                transition: "0.3 all ease",
                                borderRadius: "8px",
                                border: "1px solid #E5E5E5",
                                "&:hover": {
                                    background: "#eee"
                                }
                            },
                            onClick: () => c([t]),
                            children: [e.jsxs(cP, {
                                sx: {
                                    margin: "0 0.5rem"
                                },
                                row: !0,
                                ycenter: !0,
                                gap: "8px",
                                children: [e.jsx(c8, {}), e.jsx(rP, {
                                    s: 14,
                                    children: t?.name
                                })]
                            }), e.jsx(cP, {
                                children: e.jsx(C, {
                                    checked: l?.id === t?.id,
                                    onClick: () => c([t]),
                                    value: t,
                                    name: "check-buttons"
                                })
                            })]
                        })
                    }, t?.id)), u && e.jsx(cP, {
                        sx: {
                            textAlign: "center",
                            my: "1rem"
                        },
                        children: e.jsx(rP, {
                            s: "16",
                            light: !0,
                            center: !0,
                            mx: "auto",
                            children: x("loadingMore")
                        })
                    })]
                }), e.jsx(L, {}), e.jsx(M, {
                    sx: {
                        p: "1rem"
                    },
                    children: e.jsxs(cP, {
                        row: !0,
                        sx: {
                            textAlign: "right",
                            margin: "16px 0 0.5rem"
                        },
                        children: [e.jsx(dP, {
                            size: "large",
                            variant: "text",
                            onClick: y,
                            sx: {
                                width: "180px"
                            },
                            children: x("common.cancel")
                        }), e.jsx(dP, {
                            onClick: () => h({
                                id: l?.id,
                                type: i,
                                cash: f.cash,
                                bank: f.bank
                            }),
                            disabled: !l?.id || m || !f.cash && !f.bank,
                            sx: {
                                width: "200px"
                            },
                            variant: "contained",
                            isLoading: m,
                            children: x("marketplace.listCommunity")
                        })]
                    })
                })]
            }) : e.jsx(cP, {
                center: !0,
                mt: "54px",
                mb: "32px",
                children: e.jsx(rP, {
                    s: "22",
                    light: !0,
                    sx: {
                        mb: "0.7rem",
                        textTransform: "capitalize"
                    },
                    children: x("common.NoDataAvailable")
                })
            })]
        })]
    })
}
const Ete = G(({
    className: t,
    ...n
}) => e.jsx(y, {
    ...n,
    classes: {
        popper: t
    }
}))(({
    theme: e
}) => ({
    "& .MuiTooltip-tooltip": {
        backgroundColor: u(e.palette.primary.main, 1),
        color: "#fff",
        padding: "8px 12px",
        borderRadius: "6px"
    },
    "& .MuiTooltip-arrow": {
        color: "#CACACA"
    }
}));

function Dte() {
    const {
        list: t,
        isLoading: n,
        search: r,
        setSearch: a,
        metadata: i,
        page: o,
        setPage: l,
        communityToBeUnlisted: d,
        setCommunityToBeUnlisted: c,
        unList: u,
        isUnListing: p
    } = Cte(), {
        state: h
    } = Ht(), m = h?.interestedCustomers ?? null, [f, g] = Dt.useState(m ?? null), {
        t: y,
        i18n: {
            language: v
        }
    } = Gn(), _ = s();