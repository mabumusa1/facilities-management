                path: "",
                title: "",
                element: e.jsx(Y5, {})
            }]
        }, {
            path: ":type/:id",
            title: "list",
            query: "parentName",
            element: e.jsx(Zt, {}),
            children: [{
                path: "",
                title: "",
                element: e.jsx(Y5, {})
            }, {
                path: "bulk-upload",
                title: "bulk-upload",
                element: e.jsx(Zt, {}),
                children: [{
                    path: "",
                    title: "",
                    element: e.jsx(T6, {})
                }, {
                    path: "bulk-upload-errors",
                    title: "bulk-upload-errors",
                    element: e.jsx(H5, {})
                }]
            }]
        }, {
            path: "communities/:communityId/:type/:id",
            title: "list",
            query: "name",
            element: e.jsx(Zt, {}),
            children: [{
                path: "",
                title: "",
                element: e.jsx(Y5, {})
            }]
        }, {
            path: ":type",
            title: "list",
            query: "name",
            element: e.jsx(Zt, {}),
            children: [{
                path: "",
                title: "",
                element: e.jsx(Y5, {})
            }, {
                path: "bulk-upload",
                title: "bulk-upload",
                element: e.jsx(T6, {})
            }]
        }, {
            path: "new-unit",
            title: "add-unit-form",
            element: e.jsx(U5, {})
        }, {
            path: "unit/details/:id",
            title: "unit-details",
            query: "name",
            element: e.jsx(Zt, {}),
            children: [{
                path: "",
                title: "unit-details",
                element: e.jsx(W5, {})
            }, {
                path: "edit-unit",
                title: "edit-unit-form",
                element: e.jsx(U5, {})
            }, {
                path: "marketplace-listing",
                title: "marketplace-listing",
                element: e.jsx(Z5, {})
            }, {
                title: "assignOwner",
                path: "owner",
                element: e.jsx(N5, {})
            }]
        }]
    }];
var G5 = (e => (e.DANGER = "danger", e.SUCCESS = "success", e.INFO = "info", e.GRAY = "gray", e.DEFAULT = "default", e))(G5 || {}),
    K5 = (e => (e[e.NEW = 30] = "NEW", e[e.ACTIVE = 31] = "ACTIVE", e[e.EXPIRED = 32] = "EXPIRED", e[e.TERMINATED = 33] = "TERMINATED", e))(K5 || {});

function Q5() {
    const {
        id: t
    } = qt(), {
        t: n
    } = Gn(), r = Ys(), a = Ft(), [i, o] = Dt.useState(!1), [s, l] = Dt.useState(!1), {
        data: d,
        isLoading: c,
        isError: u
    } = tl([rH, t], async () => await t6(Number(t)), {
        useErrorBoundary: !1
    }), {
        mutate: p,
        isLoading: h
    } = nl({
        mutationFn: e => (async e => {
            try {
                await po(`/api-management/rf/leases/${e}`)
            } catch (t) {
                throw t
            }
        })(e),
        onSuccess: () => {
            o(!1), l(!0), r.invalidateQueries([_H]), r.invalidateQueries([CH]), r.invalidateQueries([vH]), r.invalidateQueries([_F]), r.invalidateQueries([aF]), r.invalidateQueries(["SUBLEASES"]), r.invalidateQueries([tF])
        },
        onError: e => {
            o(!1), Zi.error(e?.response?.data?.message)
        }
    }), m = Dt.useCallback(() => {
        p(+t)
    }, []);
    u && Zi.error(n("leasing.leaseFetchFailure"), {
        toastId: "fetchLeaseError"
    });
    const f = {
            title: n("leasing.viewPdf"),
            onClick: () => window.open(d?.pdf_url, "_blank"),
            variant: "contained",
            sx: {
                fontWeight: "normal"
            }
        },
        g = {
            title: n("leasing.terminate"),
            onClick: () => {
                a(`/leasing/details/${t}/terminate-lease`)
            },
            variant: "contained",
            color: "error",
            sx: {
                fontWeight: "normal"
            },
            hasDivider: !0
        },
        y = {
            title: n("leasing.deleteLease"),
            onClick: () => o(!0),
            variant: "outlined",
            color: "error"
        },
        v = t => ({
            title: n("leasing.actions"),
            variant: "contained",
            color: "primary",
            icon: e.jsx(Zw, {
                sx: {
                    fontSize: "23px !important"
                }
            }),
            menu: t
        }),
        _ = {
            [K5.NEW]: [y, v([f, g])],
            [K5.ACTIVE]: [y, v([f, g])],
            [K5.EXPIRED]: [y, d?.isRenew && !d?.isOld || d?.tenant?.isMoveOut ? v([f, d?.isRenew && !d?.isOld ? {
                title: n("leasing.renew"),
                onClick: () => a(`/leasing/form?type=${d?.units?.[0]?.category?.toLowerCase()}&id=${d?.id}`),
                variant: "outlined",
                sx: {
                    fontWeight: "normal"
                }
            } : null, d?.tenant?.isMoveOut ? {
                title: n("leasing.moveOut"),
                onClick: () => {
                    a(`/leasing/${t}/moveout-lease`)
                },
                variant: "outlined",
                color: "error",
                sx: {
                    fontWeight: "normal"
                },
                hasDivider: !0
            } : null]) : {
                ...f,
                variant: "contained",
                sx: {
                    color: e => e?.palette?.primary?.main,
                    backgroundColor: e => `${e?.palette?.primary?.main}22`,
                    "&:hover": {
                        backgroundColor: e => `${e?.palette?.primary?.main}22`,
                        opacity: .8
                    }
                }
            }],
            [K5.TERMINATED]: [y, {
                title: n("leasing.viewPdf"),
                onClick: () => window.open(d?.pdf_url, "_blank"),
                variant: "contained",
                sx: {
                    color: e => e?.palette?.primary?.main,
                    backgroundColor: e => `${e?.palette?.primary?.main}22`,
                    "&:hover": {
                        backgroundColor: e => `${e?.palette?.primary?.main}22`,
                        opacity: .8
                    }
                }
            }]
        };
    return {
        lease: d,
        isLoading: c,
        isRemovingLease: h,
        leaseActionStrategy: _,
        onDelete: m,
        goBack: () => {
            a(-1)
        },
        showDeleteDialogue: i,
        setShowDeleteDialogue: o,
        showDeleteConfirmation: s,
        setShowDeleteConfirmation: l
    }
}
const J5 = {
    text: {
        [K5.NEW]: "#002A37",
        [K5.ACTIVE]: "#0A9458",
        [K5.TERMINATED]: "#00000080",
        [K5.EXPIRED]: "#812222"
    },
    bg: {
        [K5.NEW]: "#EBF0F1",
        [K5.ACTIVE]: "#EDFAF4",
        [K5.TERMINATED]: "#F0F0F0",
        [K5.EXPIRED]: "#FFE5E5"
    }
};

function X5({
    status: t,
    actions: n
}) {
    const {
        t: r
    } = Gn();
    return e.jsxs(e.Fragment, {
        children: [e.jsx(IQ, {
            sx: {
                width: "fit-content"
            }
        }), e.jsxs(cP, {
            component: "header",
            row: !0,
            justifyContent: "space-between",
            ycenter: !0,
            children: [e.jsxs(cP, {
                row: !0,
                ycenter: !0,
                gap: "16px",
                children: [e.jsx(rP, {
                    component: "h1",
                    s: 36,
                    children: r("breadcrumb.leaseDetails")
                }), e.jsx(ve, {
                    label: t?.name?.toLowerCase(),
                    sx: {
                        backgroundColor: J5.bg[t?.id],
                        color: J5.text[t?.id],
                        borderRadius: "32px",
                        textTransform: "capitalize"
                    }
                })]
            }), e.jsx(cP, {
                children: n?.filter(e => null != e)?.map((t, n) => t?.menu ? e.jsx(k2, {
                    sx: {
                        width: "140px",
                        height: "53px"
                    },
                    variant: t.variant,
                    color: t.color,
                    title: r("leasing.actions"),
                    listData: t?.menu
                }) : e.jsx(dP, {
                    sx: {
                        marginRight: "16px",
                        padding: "12px 24px",
                        fontSize: "16px",
                        ...t?.sx || {}
                    },
                    onClick: t?.onClick,
                    variant: t?.variant,
                    color: t?.color,
                    disabled: t?.disabled,
                    children: t?.title
                }, t?.title))
            })]
        })]
    })
}

function e8({
    title: t,
    sx: n,
    children: r,
    cols: a
}) {
    return e.jsxs(cP, {
        sx: {
            borderRadius: "16px",
            border: "1px solid #E3E3E3",
            padding: "36px",
            backgroundColor: "#fff",
            ...n
        },
        children: [e.jsx(rP, {
            s: 24,
            mb: "24px",
            children: t
        }), e.jsx(cP, {
            sx: {
                display: "grid",
                gridTemplateColumns: `repeat(${a}, 1fr)`,
                rowGap: "24px"
            },
            children: r
        })]
    })
}
const t8 = t => e.jsx(i, {
        ...t,
        children: e.jsx("svg", {
            viewBox: "0 0 15 17",
            fill: "none",
            xmlns: "http://www.w3.org/2000/svg",
            children: e.jsx("path", {
                d: "M7.5 14.6749L11.2125 10.9624C11.9467 10.2282 12.4466 9.29273 12.6492 8.27435C12.8517 7.25596 12.7477 6.20039 12.3503 5.24111C11.9529 4.28183 11.28 3.46192 10.4167 2.88507C9.55334 2.30821 8.53833 2.00032 7.5 2.00032C6.46167 2.00032 5.44666 2.30821 4.58332 2.88507C3.71997 3.46192 3.04706 4.28183 2.64969 5.24111C2.25231 6.20039 2.14831 7.25596 2.35084 8.27435C2.55337 9.29273 3.05333 10.2282 3.7875 10.9624L7.5 14.6749ZM7.5 16.7959L2.727 12.0229C1.78301 11.0789 1.14014 9.8762 0.879696 8.56683C0.619253 7.25746 0.75293 5.90026 1.26382 4.66687C1.77472 3.43347 2.63988 2.37927 3.74991 1.63757C4.85994 0.895878 6.16498 0.5 7.5 0.5C8.83502 0.5 10.1401 0.895878 11.2501 1.63757C12.3601 2.37927 13.2253 3.43347 13.7362 4.66687C14.2471 5.90026 14.3808 7.25746 14.1203 8.56683C13.8599 9.8762 13.217 11.0789 12.273 12.0229L7.5 16.7959ZM7.5 8.74994C7.89783 8.74994 8.27936 8.59191 8.56066 8.3106C8.84197 8.0293 9 7.64777 9 7.24994C9 6.85212 8.84197 6.47059 8.56066 6.18928C8.27936 5.90798 7.89783 5.74994 7.5 5.74994C7.10218 5.74994 6.72065 5.90798 6.43934 6.18928C6.15804 6.47059 6 6.85212 6 7.24994C6 7.64777 6.15804 8.0293 6.43934 8.3106C6.72065 8.59191 7.10218 8.74994 7.5 8.74994ZM7.5 10.2499C6.70435 10.2499 5.94129 9.93387 5.37868 9.37126C4.81607 8.80865 4.5 8.04559 4.5 7.24994C4.5 6.45429 4.81607 5.69123 5.37868 5.12862C5.94129 4.56601 6.70435 4.24994 7.5 4.24994C8.29565 4.24994 9.05871 4.56601 9.62132 5.12862C10.1839 5.69123 10.5 6.45429 10.5 7.24994C10.5 8.04559 10.1839 8.80865 9.62132 9.37126C9.05871 9.93387 8.29565 10.2499 7.5 10.2499Z",
                fill: "#969798"
            })
        })
    }),
    n8 = t => e.jsx(i, {
        ...t,
        children: e.jsx("svg", {
            viewBox: "0 0 19 19",
            fill: "none",
            xmlns: "http://www.w3.org/2000/svg",
            children: e.jsx("path", {
                d: "M16.6237 16.625H2.3737C2.16373 16.625 1.96237 16.5416 1.81391 16.3931C1.66544 16.2447 1.58203 16.0433 1.58203 15.8333V9.88554C1.58202 9.77242 1.60625 9.66061 1.65309 9.55765C1.69993 9.45468 1.76829 9.36295 1.85357 9.28863L4.7487 6.764V3.16667C4.7487 2.9567 4.83211 2.75534 4.98057 2.60687C5.12904 2.45841 5.3304 2.375 5.54036 2.375H16.6237C16.8337 2.375 17.035 2.45841 17.1835 2.60687C17.332 2.75534 17.4154 2.9567 17.4154 3.16667V15.8333C17.4154 16.0433 17.332 16.2447 17.1835 16.3931C17.035 16.5416 16.8337 16.625 16.6237 16.625ZM7.1237 15.0417H9.4987V10.2458L6.33203 7.48442L3.16536 10.2458V15.0417H5.54036V11.875H7.1237V15.0417ZM11.082 15.0417H15.832V3.95833H6.33203V5.64221C6.51728 5.64221 6.70332 5.70713 6.85216 5.83775L10.8105 9.28863C10.8958 9.36295 10.9641 9.45468 11.011 9.55765C11.0578 9.66061 11.082 9.77242 11.082 9.88554V15.0417ZM12.6654 8.70833H14.2487V10.2917H12.6654V8.70833ZM12.6654 11.875H14.2487V13.4583H12.6654V11.875ZM12.6654 5.54167H14.2487V7.125H12.6654V5.54167ZM9.4987 5.54167H11.082V7.125H9.4987V5.54167Z",
                fill: "#969798"
            })
        })
    }),
    r8 = t => e.jsx(i, {
        ...t,
        children: e.jsx("svg", {
            viewBox: "0 0 19 17",
            fill: "none",
            xmlns: "http://www.w3.org/2000/svg",
            children: e.jsx("path", {
                d: "M16.6263 14.834H18.2096V16.4173H0.792969V14.834H2.3763V1.37565C2.3763 1.16569 2.45971 0.964324 2.60818 0.815858C2.75664 0.667392 2.95801 0.583984 3.16797 0.583984H15.8346C16.0446 0.583984 16.246 0.667392 16.3944 0.815858C16.5429 0.964324 16.6263 1.16569 16.6263 1.37565V14.834ZM15.043 14.834V2.16732H3.95964V14.834H15.043ZM6.33464 7.70898H8.70964V9.29232H6.33464V7.70898ZM6.33464 4.54232H8.70964V6.12565H6.33464V4.54232ZM6.33464 10.8757H8.70964V12.459H6.33464V10.8757ZM10.293 10.8757H12.668V12.459H10.293V10.8757ZM10.293 7.70898H12.668V9.29232H10.293V7.70898ZM10.293 4.54232H12.668V6.12565H10.293V4.54232Z",
                fill: "#969798"
            })
        })
    }),
    a8 = t => e.jsx(i, {
        ...t,
        inheritViewBox: !0,
        children: e.jsx("svg", {
            viewBox: "0 0 16 16",
            fill: "none",
            xmlns: "http://www.w3.org/2000/svg",
            children: e.jsx("path", {
                d: "M11.9492 2.25H14.9492C15.1481 2.25 15.3389 2.32902 15.4795 2.46967C15.6202 2.61032 15.6992 2.80109 15.6992 3V15C15.6992 15.1989 15.6202 15.3897 15.4795 15.5303C15.3389 15.671 15.1481 15.75 14.9492 15.75H1.44922C1.25031 15.75 1.05954 15.671 0.918889 15.5303C0.778236 15.3897 0.699219 15.1989 0.699219 15V3C0.699219 2.80109 0.778236 2.61032 0.918889 2.46967C1.05954 2.32902 1.25031 2.25 1.44922 2.25H4.44922V0.75H5.94922V2.25H10.4492V0.75H11.9492V2.25ZM14.1992 8.25H2.19922V14.25H14.1992V8.25ZM10.4492 3.75H5.94922V5.25H4.44922V3.75H2.19922V6.75H14.1992V3.75H11.9492V5.25H10.4492V3.75ZM3.69922 9.75H5.19922V11.25H3.69922V9.75ZM7.44922 9.75H8.94922V11.25H7.44922V9.75ZM11.1992 9.75H12.6992V11.25H11.1992V9.75Z",
                fill: "#969798"
            })
        })
    }),
    i8 = t => e.jsx(i, {
        ...t,
        children: e.jsx("svg", {
            viewBox: "0 0 16 16",
            fill: "none",
            xmlns: "http://www.w3.org/2000/svg",
            children: e.jsx("path", {
                d: "M5 5V11H11V5H5ZM3.5 3.5H12.5V12.5H3.5V3.5ZM3.5 0.5H5V2.75H3.5V0.5ZM3.5 13.25H5V15.5H3.5V13.25ZM0.5 3.5H2.75V5H0.5V3.5ZM0.5 11H2.75V12.5H0.5V11ZM13.25 3.5H15.5V5H13.25V3.5ZM13.25 11H15.5V12.5H13.25V11ZM11 0.5H12.5V2.75H11V0.5ZM11 13.25H12.5V15.5H11V13.25Z",
                fill: "#969798"
            })
        })
    }),
    o8 = t => e.jsx(i, {
        ...t,
        children: e.jsx("svg", {
            viewBox: "0 0 15 15",
            fill: "none",
            xmlns: "http://www.w3.org/2000/svg",
            children: e.jsx("path", {
                fillRule: "evenodd",
                clipRule: "evenodd",
                d: "M0.5 0C0.367392 0 0.240215 0.0526784 0.146447 0.146447C0.0526784 0.240215 0 0.367392 0 0.5L0 14.5C0 14.6326 0.0526784 14.7598 0.146447 14.8536C0.240215 14.9473 0.367392 15 0.5 15H4.5C4.63261 15 4.75979 14.9473 4.85355 14.8536C4.94732 14.7598 5 14.6326 5 14.5V5H14.5C14.6326 5 14.7598 4.94732 14.8536 4.85355C14.9473 4.75979 15 4.63261 15 4.5V0.5C15 0.367392 14.9473 0.240215 14.8536 0.146447C14.7598 0.0526784 14.6326 0 14.5 0L0.5 0ZM1 4.075V1H4.075V4.075H1ZM1 4.925V14H4V12.925H2.75C2.63728 12.925 2.52918 12.8802 2.44948 12.8005C2.36978 12.7208 2.325 12.6127 2.325 12.5C2.325 12.3873 2.36978 12.2792 2.44948 12.1995C2.52918 12.1198 2.63728 12.075 2.75 12.075H4V10.925H2.25C2.13728 10.925 2.02918 10.8802 1.94948 10.8005C1.86978 10.7208 1.825 10.6127 1.825 10.5C1.825 10.3873 1.86978 10.2792 1.94948 10.1995C2.02918 10.1198 2.13728 10.075 2.25 10.075H4V8.925H2.75C2.63728 8.925 2.52918 8.88022 2.44948 8.80052C2.36978 8.72082 2.325 8.61272 2.325 8.5C2.325 8.38728 2.36978 8.27918 2.44948 8.19948C2.52918 8.11978 2.63728 8.075 2.75 8.075H4V6.925H2.75C2.63728 6.925 2.52918 6.88022 2.44948 6.80052C2.36978 6.72082 2.325 6.61272 2.325 6.5C2.325 6.38728 2.36978 6.27918 2.44948 6.19948C2.52918 6.11978 2.63728 6.075 2.75 6.075H4V4.925H1ZM4.925 4H6.075V2.75C6.075 2.63728 6.11978 2.52918 6.19948 2.44948C6.27918 2.36978 6.38728 2.325 6.5 2.325C6.61272 2.325 6.72082 2.36978 6.80052 2.44948C6.88022 2.52918 6.925 2.63728 6.925 2.75V4H8.075V2.75C8.075 2.63728 8.11978 2.52918 8.19948 2.44948C8.27918 2.36978 8.38728 2.325 8.5 2.325C8.61272 2.325 8.72082 2.36978 8.80052 2.44948C8.88022 2.52918 8.925 2.63728 8.925 2.75V4H10.075V2.25C10.075 2.13728 10.1198 2.02918 10.1995 1.94948C10.2792 1.86978 10.3873 1.825 10.5 1.825C10.6127 1.825 10.7208 1.86978 10.8005 1.94948C10.8802 2.02918 10.925 2.13728 10.925 2.25V4H12.075V2.75C12.075 2.63728 12.1198 2.52918 12.1995 2.44948C12.2792 2.36978 12.3873 2.325 12.5 2.325C12.6127 2.325 12.7208 2.36978 12.8005 2.44948C12.8802 2.52918 12.925 2.63728 12.925 2.75V4H14V1H4.925V4Z",
                fill: "#969798"
            })
        })
    }),
    s8 = t => e.jsx(i, {
        ...t,
        children: e.jsx("svg", {
            viewBox: "0 0 14 14",
            fill: "none",
            xmlns: "http://www.w3.org/2000/svg",
            children: e.jsx("path", {
                d: "M12.25 12.25V1H9.25V0.25H1.75V12.25H0.25V13.75H9.25V2.5H10.75V13.75H13.75V12.25H12.25ZM7.75 12.25H3.25V1.75H7.75V12.25ZM5.5 6.25H7V7.75H5.5V6.25Z",
                fill: "#969798"
            })
        })
    }),
    l8 = t => e.jsx(i, {
        ...t,
        children: e.jsx("svg", {
            viewBox: "0 0 20 20",
            fill: "none",
            xmlns: "http://www.w3.org/2000/svg",
            children: e.jsx("path", {
                d: "M15 4H20V6H18V19C18 19.2652 17.8946 19.5196 17.7071 19.7071C17.5196 19.8946 17.2652 20 17 20H3C2.73478 20 2.48043 19.8946 2.29289 19.7071C2.10536 19.5196 2 19.2652 2 19V6H0V4H5V1C5 0.734784 5.10536 0.48043 5.29289 0.292893C5.48043 0.105357 5.73478 0 6 0H14C14.2652 0 14.5196 0.105357 14.7071 0.292893C14.8946 0.48043 15 0.734784 15 1V4ZM16 6H4V18H16V6ZM7 9H9V15H7V9ZM11 9H13V15H11V9ZM7 2V4H13V2H7Z",
                fill: "#FF4242"
            })
        })
    }),
    d8 = t => {
        const n = s(),
            r = n?.palette?.primary?.main;
        return e.jsx(i, {
            ...t,
            children: e.jsx("svg", {
                viewBox: "0 0 14 15",
                fill: "none",
                xmlns: "http://www.w3.org/2000/svg",
                children: e.jsx("path", {
                    d: "M9.796 5.76407L8.7355 4.70357L1.75 11.6891V12.7496H2.8105L9.796 5.76407ZM10.8565 4.70357L11.917 3.64307L10.8565 2.58257L9.796 3.64307L10.8565 4.70357ZM3.4315 14.2496H0.25V11.0673L10.3263 0.991074C10.4669 0.850471 10.6576 0.771484 10.8565 0.771484C11.0554 0.771484 11.2461 0.850471 11.3868 0.991074L13.5085 3.11282C13.6491 3.25347 13.7281 3.4442 13.7281 3.64307C13.7281 3.84195 13.6491 4.03268 13.5085 4.17332L3.43225 14.2496H3.4315Z",
                    fill: r
                })
            })
        })
    },
    c8 = t => e.jsx(i, {
        ...t,
        children: e.jsx("svg", {
            viewBox: "0 0 31 29",
            fill: "none",
            xmlns: "http://www.w3.org/2000/svg",
            children: e.jsx("path", {
                d: "M23.207 25.2917H26.2904V12.9583H17.0404V25.2917H20.1237V16.0417H23.207V25.2917ZM1.6237 25.2917V2.16667C1.6237 1.75779 1.78612 1.36566 2.07524 1.07654C2.36436 0.787425 2.75649 0.625 3.16536 0.625H24.7487C25.1576 0.625 25.5497 0.787425 25.8388 1.07654C26.1279 1.36566 26.2904 1.75779 26.2904 2.16667V9.875H29.3737V25.2917H30.9154V28.375H0.0820312V25.2917H1.6237ZM7.79037 12.9583V16.0417H10.8737V12.9583H7.79037ZM7.79037 19.125V22.2083H10.8737V19.125H7.79037ZM7.79037 6.79167V9.875H10.8737V6.79167H7.79037Z",
                fill: "#CACACA"
            })
        })
    }),
    u8 = t => e.jsx(i, {
        ...t,
        children: e.jsx("svg", {
            viewBox: "0 0 66 70",
            fill: "none",
            xmlns: "http://www.w3.org/2000/svg",
            children: e.jsx("path", {
                d: "M45 58.166C47.5556 58.166 49.7222 57.291 51.5 55.541C53.2778 53.791 54.1667 51.6105 54.1667 48.9993C54.1667 46.4438 53.2778 44.2771 51.5 42.4993C49.7222 40.7216 47.5556 39.8327 45 39.8327C42.3889 39.8327 40.2083 40.7216 38.4583 42.4993C36.7083 44.2771 35.8333 46.4438 35.8333 48.9993C35.8333 51.6105 36.7083 53.791 38.4583 55.541C40.2083 57.291 42.3889 58.166 45 58.166ZM62 69.4993L53.0833 60.666C51.9167 61.4438 50.6528 62.0549 49.2917 62.4994C47.9306 62.9438 46.5 63.166 45 63.166C41.0556 63.166 37.7083 61.791 34.9583 59.041C32.2083 56.291 30.8333 52.9438 30.8333 48.9993C30.8333 45.1105 32.2083 41.7771 34.9583 38.9993C37.7083 36.2216 41.0556 34.8327 45 34.8327C48.8889 34.8327 52.2222 36.2216 55 38.9993C57.7778 41.7771 59.1667 45.1105 59.1667 48.9993C59.1667 50.5549 58.9306 52.0132 58.4583 53.3743C57.9861 54.7355 57.3611 55.9994 56.5833 57.166L65.4167 65.9994L62 69.4993ZM5 67.3327C3.66667 67.3327 2.5 66.8327 1.5 65.8327C0.5 64.8327 0 63.666 0 62.3327V5.66602C0 4.33268 0.5 3.16602 1.5 2.16602C2.5 1.16602 3.66667 0.666016 5 0.666016H35.0833L53.3333 18.916V31.7493C52 31.1382 50.6389 30.666 49.25 30.3327C47.8611 29.9993 46.4444 29.8327 45 29.8327C42.6667 29.8327 40.4861 30.1938 38.4583 30.916C36.4306 31.6382 34.6111 32.666 33 33.9993H13.25V38.9993H28.6667C27.8333 40.3327 27.1806 41.7771 26.7083 43.3327C26.2361 44.8882 25.9444 46.4993 25.8333 48.166H13.25V53.166H26.25C27.0278 56.4993 28.5694 59.4438 30.875 61.9993C33.1806 64.5549 36 66.3327 39.3333 67.3327H5ZM32.5833 21.166H48.3333L32.5833 5.66602V21.166Z",
                fill: "#E3E3E3"
            })
        })
    }),
    p8 = t => e.jsx(i, {
        ...t,
        children: e.jsx("svg", {
            viewBox: "0 0 26 33",
            fill: "none",
            xmlns: "http://www.w3.org/2000/svg",
            children: e.jsx("path", {
                d: "M25.0892 32.2739H22.0742V29.2588C22.0742 28.0593 21.5977 26.909 20.7495 26.0608C19.9014 25.2127 18.751 24.7362 17.5516 24.7362H8.5064C7.30694 24.7362 6.1566 25.2127 5.30845 26.0608C4.4603 26.909 3.98381 28.0593 3.98381 29.2588V32.2739H0.96875V29.2588C0.96875 27.2597 1.76289 25.3425 3.17648 23.9289C4.59006 22.5153 6.50729 21.7211 8.5064 21.7211H17.5516C19.5507 21.7211 21.4679 22.5153 22.8815 23.9289C24.2951 25.3425 25.0892 27.2597 25.0892 29.2588V32.2739ZM13.029 18.7061C11.8412 18.7061 10.665 18.4721 9.56755 18.0176C8.47014 17.563 7.473 16.8967 6.63308 16.0568C5.79316 15.2169 5.1269 14.2198 4.67233 13.1223C4.21777 12.0249 3.98381 10.8487 3.98381 9.6609C3.98381 8.47307 4.21777 7.29688 4.67233 6.19946C5.1269 5.10205 5.79316 4.10492 6.63308 3.26499C7.473 2.42507 8.47014 1.75881 9.56755 1.30425C10.665 0.849683 11.8412 0.615723 13.029 0.615723C15.4279 0.615723 17.7286 1.56869 19.4249 3.26499C21.1212 4.9613 22.0742 7.26197 22.0742 9.6609C22.0742 12.0598 21.1212 14.3605 19.4249 16.0568C17.7286 17.7531 15.4279 18.7061 13.029 18.7061ZM13.029 15.691C14.6283 15.691 16.1621 15.0557 17.2929 13.9248C18.4238 12.794 19.0591 11.2602 19.0591 9.6609C19.0591 8.06162 18.4238 6.52783 17.2929 5.39696C16.1621 4.2661 14.6283 3.63078 13.029 3.63078C11.4297 3.63078 9.89592 4.2661 8.76505 5.39696C7.63418 6.52783 6.99887 8.06162 6.99887 9.6609C6.99887 11.2602 7.63418 12.794 8.76505 13.9248C9.89592 15.0557 11.4297 15.691 13.029 15.691Z",
                fill: "#CACACA"
            })
        })
    }),
    h8 = t => e.jsx(i, {
        ...t,
        children: e.jsx("svg", {
            viewBox: "0 0 33 31",
            fill: "none",
            xmlns: "http://www.w3.org/2000/svg",
            children: e.jsx("path", {
                d: "M28.5 18.5V23H33V26H28.5V30.5H25.5V26H21V23H25.5V18.5H28.5ZM28.512 0.5C29.334 0.5 30 1.1675 30 1.9895V15.5H27V3.5H3V24.4985L18 9.5L22.5 14V18.2435L18 13.7435L7.2405 24.5H18V27.5H1.488C1.09322 27.4996 0.714745 27.3425 0.435734 27.0632C0.156723 26.7839 -2.00183e-07 26.4053 0 26.0105V1.9895C0.00274507 1.59557 0.16035 1.21853 0.438769 0.939826C0.717188 0.661127 1.09407 0.503142 1.488 0.5H28.512ZM9 6.5C9.79565 6.5 10.5587 6.81607 11.1213 7.37868C11.6839 7.94129 12 8.70435 12 9.5C12 10.2956 11.6839 11.0587 11.1213 11.6213C10.5587 12.1839 9.79565 12.5 9 12.5C8.20435 12.5 7.44129 12.1839 6.87868 11.6213C6.31607 11.0587 6 10.2956 6 9.5C6 8.70435 6.31607 7.94129 6.87868 7.37868C7.44129 6.81607 8.20435 6.5 9 6.5Z",
                fill: "#CACACA"
            })
        })
    }),
    m8 = t => e.jsx(i, {
        ...t,
        children: e.jsx("svg", {
            viewBox: "0 0 21 21",
            fill: "none",
            xmlns: "http://www.w3.org/2000/svg",
            children: e.jsx("path", {
                d: "M10.5 20.5C4.977 20.5 0.5 16.023 0.5 10.5C0.5 4.977 4.977 0.5 10.5 0.5C16.023 0.5 20.5 4.977 20.5 10.5C20.5 16.023 16.023 20.5 10.5 20.5ZM10.5 18.5C12.6217 18.5 14.6566 17.6571 16.1569 16.1569C17.6571 14.6566 18.5 12.6217 18.5 10.5C18.5 8.37827 17.6571 6.34344 16.1569 4.84315C14.6566 3.34285 12.6217 2.5 10.5 2.5C8.37827 2.5 6.34344 3.34285 4.84315 4.84315C3.34285 6.34344 2.5 8.37827 2.5 10.5C2.5 12.6217 3.34285 14.6566 4.84315 16.1569C6.34344 17.6571 8.37827 18.5 10.5 18.5ZM9.5 13.5H11.5V15.5H9.5V13.5ZM9.5 5.5H11.5V11.5H9.5V5.5Z",
                fill: "#CACACA"
            })
        })
    }),
    f8 = {
        paid: bU,
        outstanding: CU,
        overdue: CU
    },
    g8 = (t, n) => "individual" === t?.tenant?.type ? [{
        title: "signUp.nationalId2",
        body: t?.tenant?.nationalId ?? "---"
    }, t?.tenant?.birthDate ? {
        title: "leasing.dateOfBirth",
        body: t?.tenant?.birthDate ? Fj(t?.tenant?.birthDate).format("DD/MM/YYYY") : "---"
    } : {
        title: "leasing.dateOfBirth",
        body: "---"
    }, t?.tenant?.nationality ? {
        title: "signUp.nationality",
        body: t?.tenant?.nationality
    } : {
        title: "signUp.nationality",
        body: "---"
    }, t?.tenant.gender ? {
        title: "gender",
        body: uZ(n(t?.tenant?.gender))
    } : null, {
        title: "editForm.email",
        body: t?.tenant?.email || "---"
    }] : [{
        title: "leasing.company_number",
        body: t?.tenant?.registrationNumber || "---"
    }, {
        title: "contacts.taxIdentifierNo",
        body: t?.tenant?.taxNumber || "---"
    }, {
        title: "leasing.nationalAddress",
        body: t?.tenant?.nationalAddress || "---"
    }, {
        title: "leasing.website",
        body: t?.tenant?.website ? e.jsx("a", {
            href: t?.tenant?.website,
            className: "website-link",
            target: "_blank",
            rel: "noopener noreferrer",
            children: t?.tenant?.website
        }) : "---"
    }],
    y8 = t => "company" === t?.tenant?.type ? [{
        title: "editForm.name",
        body: t?.tenant?.representative?.name || "---"
    }, {
        title: "signUp.nationalId2",
        body: t?.tenant?.representative?.nationalId || "---"
    }, {
        title: "editForm.phone",
        body: t?.tenant?.representative?.phone ? e.jsx("span", {
            dir: "ltr",
            children: t?.tenant?.representative?.phone
        }) : "---"
    }, {
        title: "leasing.authorizationNo",
        body: t?.tenant?.representative?.authorizationNo || "---"
    }, t?.tenant?.representative?.birthDate ? {
        title: "leasing.dateOfBirth",
        body: Fj(t?.tenant?.representative?.birthDate).format("DD/MM/YYYY")
    } : {
        title: "leasing.dateOfBirth",
        body: "---"
    }, t?.tenant?.representative?.nationality ? {
        title: "signUp.nationality",
        body: t?.tenant?.representative?.nationality
    } : {
        title: "signUp.nationality",
        body: "---"
    }, t?.tenant?.representative?.gender ? {
        title: "gender",
        body: uZ(t?.tenant?.representative?.gender)
    } : null, t?.tenant?.representative?.email ? {
        title: "Email",
        body: t?.tenant?.representative?.email
    } : {
        title: "Email",
        body: "---"
    }] : null,
    v8 = (e, t) => [{
        title: "leasing.contractType",
        body: e?.contract?.type
    }, {
        title: "leasing.rentalType",
        body: e?.contract?.rentalTypeValue ? t(`leaseForm.${e.contract.rentalTypeValue}Rental`) : "---"
    }, {
        title: "leasing.contractNumber",
        body: e?.contract?.number
    }, e?.contract?.owner ? {
        title: "leasing.dealOwner",
        body: e?.contract?.owner
    } : null, e?.contract?.creationDate ? {
        title: "leasing.creationDate",
        body: e?.contract?.creationDate ? Fj(e?.contract?.creationDate).format("DD/MM/YYYY") : "---"
    } : null, {
        title: "leasing.handoverDate",
        body: e?.contract?.handoverDate ? Fj(e?.contract?.handoverDate).format("DD/MM/YYYY") : "---"
    }, {
        title: "leasing.contractStartDate",
        body: e?.contract?.startDate ? Fj(e?.contract?.startDate).format("DD/MM/YYYY") : "---"
    }, {
        title: "leasing.contractEndDate",
        body: e?.contract?.endDate ? Fj(e?.contract?.endDate).format("DD/MM/YYYY") : "---"
    }, e?.contract?.terminatedDate && e.status === K5.TERMINATED ? {
        title: "leasing.terminatedDate",
        body: e?.contract?.terminatedDate ? Fj(e?.contract?.terminatedDate).format("DD/MM/YYYY") : "---"
    } : null, e?.contract?.moveOutDate && e?.status === K5.EXPIRED ? {
        title: "leasing.moveOutDate",
        body: e?.contract?.moveOutDate ? Fj(e?.contract?.moveOutDate).format("DD/MM/YYYY") : "---"
    } : null, {
        title: "leasing.rentFreePeriod",
        body: e?.contract?.freePeriod ?? "---"
    }, {
        title: "leasing.paymentSchedule",
        body: e?.contract?.rentalSchedule
    }, e?.contract?.rentalTypeValue === Z4.YEARLY ? {
        title: "leasing.annualIncrease",
        body: t(e?.contract?.rentIncrease ? "common.yes" : "common.no")
    } : null, e?.contract?.fitOutStatus ? {
        title: "leasing.fitOut",
        body: e?.contract?.fitOutStatus
    } : null],
    _8 = (t, n) => t?.escalation ? {
        headers: [n("leasing.year"), n("leasing.escalationType"), n("leasing.amountBeforeIncrease"), n("leasing.increaseAmount"), n("leasing.amountAfterIncrease")],
        rows: t?.escalation?.map(t => [e.jsxs(hp, {
            s: "16",
            children: [t.year, (t.start_date || t.end_date) && e.jsxs(hp, {
                s: "14",
                light: !0,
                gray: !0,
                children: [t.start_date ? Fj(t.start_date).format("DD/MM/YYYY") : "", " -", " ", t.end_date ? Fj(t.end_date).format("DD/MM/YYYY") : ""]
            })]
        }, t.year), e.jsx(hp, {
            s: "14",
            light: !0,
            children: t.type ? n(`leasing.${t.type}`) : "---"
        }, t.year + t.type), e.jsx(hp, {
            s: "14",
            light: !0,
            currency: !!t.amountBeforeIncrease,
            children: t.amountBeforeIncrease ? d6(Number(t.amountBeforeIncrease)?.toFixed(2)) : "---"
        }, t.amountBeforeIncrease), e.jsx(hp, {
            s: "14",
            light: !0,
            currency: !!t.increaseAmount,
            children: t.increaseAmount ? d6(Number(t.increaseAmount)?.toFixed(2)) : "---"
        }, t.increaseAmount), e.jsx(hp, {
            s: "14",
            light: !0,
            currency: !!t.amountAfterIncrease,
            children: t.amountAfterIncrease ? d6(Number(t.amountAfterIncrease)?.toFixed(2)) : "---"
        }, t.amountAfterIncrease)])
    } : null,
    x8 = (e, t) => {
        if (e?.isPaid) return t("leasing.paid");
        const n = e?.date ? Fj(e.date, ["YYYY-MM-DD", "DD-MM-YYYY"], !0) : null;
        return n?.isValid() && n.isBefore(Fj(), "day") ? t("leasing.overdue") : t("leasing.outstanding")
    },
    b8 = e => {
        if (e?.isPaid) return "paid";
        const t = e?.date ? Fj(e.date, ["YYYY-MM-DD", "DD-MM-YYYY"], !0) : null;
        return t?.isValid() && t.isBefore(Fj(), "day") ? "overdue" : "outstanding"
    },
    w8 = (t, n) => t?.contract?.rentalTypeValue === Z4.MONTHLY || t?.contract?.rentalTypeValue === Z4.DAILY ? {
        headers: [n("leasing.rentAmountNet"), n("leasing.vatAmount"), n("leasing.totalAmount"), n("leasing.dueDate"), n("leasing.paymentStatus")],
        rows: t?.payment?.map(t => [e.jsx(hp, {
            s: "14",
            currency: !0,
            children: null != t.rent ? d6(Number(t.rent).toFixed(2)) : "---"
        }, `${t.id}-rent`), e.jsx(hp, {
            s: "14",
            light: !0,
            currency: !0,
            children: null != t.tax ? d6(Number(t.tax).toFixed(2)) : "---"
        }, `${t.id}-tax`), e.jsx(hp, {
            s: "14",
            light: !0,
            currency: !0,
            children: null != t.total ? d6(Number(t.total).toFixed(2)) : "---"
        }, `${t.id}-total`), e.jsx(hp, {
            s: "16",
            light: !0,
            children: t.date ? Fj(t.date).format("DD-MM-YYYY") : "---"
        }, `${t.id}-date`), e.jsx(rh, {
            title: x8(t, n),
            variant: f8[b8(t)]
        }, `${t.id}-status`)])
    } : {
        headers: [n("leasing.rent"), n("leasing.tax"), n("leasing.additionalFees"), n("leasing.totalAmount"), n("leasing.dueDate"), n("leasing.paymentStatus")],
        rows: t?.payment?.map(t => [e.jsx(hp, {
            s: "14",
            currency: !0,
            children: null != t.rent ? d6(Number(t.rent).toFixed(2)) : "---"
        }, `${t.id}-rent`), e.jsx(hp, {
            s: "14",
            light: !0,
            currency: !0,
            children: null != t.tax ? d6(Number(t.tax).toFixed(2)) : "---"
        }, `${t.id}-tax`), e.jsx(hp, {
            s: "14",
            light: !0,
            currency: !0,
            children: null != t.additionalFees ? d6(Number(t.additionalFees).toFixed(2)) : "---"
        }, `${t.id}-additionalFees`), e.jsx(hp, {
            s: "14",
            light: !0,
            currency: !0,
            children: null != t.total ? d6(Number(t.total).toFixed(2)) : "---"
        }, `${t.id}-total`), e.jsx(hp, {
            s: "16",
            light: !0,
            children: t.date ? Fj(t.date).format("DD-MM-YYYY") : "---"
        }, `${t.id}-date`), e.jsx(rh, {
            title: x8(t, n),
            variant: f8[b8(t)]
        }, `${t.id}-status`)])
    },
    C8 = t => [{
        title: "leasing.depositAmount",
        body: e.jsx(hp, {
            s: "16",
            currency: !0,
            children: t?.deposit?.amount ? d6(Number(t?.deposit?.amount).toFixed(2)) : "---"
        })
    }, {
        title: "leasing.dueDate",
        body: t?.deposit?.date ? Fj(t?.deposit?.date).format("DD/MM/YYYY") : "---"
    }],
    M8 = (e, t) => ({
        tenant: g8(e, t),
        companyRepresentative: y8(e),
        details: v8(e, t),
        escalation: _8(e, t),
        payment: w8(e, t),
        deposit: C8(e),
        tsAndCs: e?.tsAndCs
    }),
    S8 = (e, t) => {
        const n = e.tenantDetailsStep?.company?.legalRepresentative?.firstName ?? "",
            r = e.tenantDetailsStep?.company?.legalRepresentative?.lastName ?? "",
            a = e.contractDatesStep?.rentalContractType,
            i = {
                [Z4.YEARLY]: t("leaseForm.yearlyRental"),
                [Z4.MONTHLY]: t("leaseForm.monthlyRental"),
                [Z4.DAILY]: t("leaseForm.dailyRental")
            },
            o = {
                [B4.SHELL_AND_CORE]: t("leaseForm.shellAndCore"),
                [B4.FIT_OUT]: t("leaseForm.fitOut"),
                [B4.NOT_APPLICABLE]: t("leaseForm.notApplicable")
            },
            s = e.leaseDetailsStep?.rentalTransactionSchedule,
            l = t(K4[s] ?? "leaseForm.upfrontPayment");
        return {
            id: 0,
            status: K5.ACTIVE,
            statusName: "Active",
            isRenew: !1,
            pdf_url: "",
            tenant: "individual" === e.tenantDetailsStep?.tenantType ? {
                type: m$.INDIVIDUAL,
                isMoveOut: !1,
                photo: null,
                name: `${e.tenantDetailsStep?.individual?.firstName??""} ${e.tenantDetailsStep?.individual?.lastName??""}`,
                nationalId: e.tenantDetailsStep?.individual?.nationalId,
                birthDate: e.tenantDetailsStep?.individual?.dateOfBirth,
                nationality: e.nationalityName,
                gender: e.tenantDetailsStep?.individual?.gender,
                email: e.tenantDetailsStep?.individual?.email,
                phone: e.tenantDetailsStep?.individual?.phoneNumber
            } : {
                type: m$.COMPANY,
                isMoveOut: !1,
                photo: e?.tenantDetailsStep?.company?.companyLogo?.[0]?.url || e?.tenantDetailsStep?.company?.companyLogo?.url,
                name_en: e.tenantDetailsStep?.company?.companyNameEn,
                name_ar: e.tenantDetailsStep?.company?.companyNameAr,
                registrationNumber: e.tenantDetailsStep?.company?.companyRegistrationNo,
                nationalAddress: e.tenantDetailsStep?.company?.nationalAddress,
                taxNumber: e.tenantDetailsStep?.company?.taxIdentifierNo,
                website: e?.website,
                representative: {
                    name: `${n} ${r}`,
                    nationalId: e.tenantDetailsStep?.company?.legalRepresentative?.nationalId,
                    phone: e.tenantDetailsStep?.company?.legalRepresentative?.phoneNumber,
                    authorizationNo: e.tenantDetailsStep?.company?.legalRepresentative?.authorizationNumber,
                    birthDate: e.tenantDetailsStep?.company?.legalRepresentative?.dateOfBirth,
                    nationality: e.nationalityName,
                    email: e.tenantDetailsStep?.company?.legalRepresentative?.email,
                    documents: e.tenantDetailsStep?.company?.legalRepresentative?.documents
                }
            },
            contract: {
                type: t(`signUp.${e.lease_unit_type}`),
                rentalType: i[a] ?? "---",
                rentalTypeValue: a,
                number: e.leaseDetailsStep.autoGenerateLeaseNumber ? t("leasing.autoGenerated") : e.leaseDetailsStep?.leaseNumber ?? "---",
                owner: e.leaseDetailsStep?.dealOwner?.[0]?.name,
                creationDate: e.contractDatesStep?.contractCreationDate ? Fj(e.contractDatesStep?.contractCreationDate).format("YYYY-MM-DD") : "",
                handoverDate: e.contractDatesStep?.handoverDate,
                startDate: e.contractDatesStep?.leaseStartDate,
                endDate: e.contractDatesStep?.leaseEndDate,
                terminatedDate: "",
                moveOutDate: "",
                freePeriod: p6(e.contractDatesStep?.handoverDate, e.contractDatesStep?.leaseStartDate, t),
                rentalSchedule: l,
                rentIncrease: null,
                fitOutStatus: o[e.leaseDetailsStep?.fitOutStatus]
            },
            deposit: e.leaseDetailsStep?.securityDeposit ? {
                amount: Number(e.leaseDetailsStep?.securityDeposit ?? 0),
                date: e.leaseDetailsStep?.securityDueDate
            } : null,
            escalation: null,
            payment: null,
            units: e.unitSelectionStep?.units,
            tsAndCs: e.leaseDetailsStep?.termsAndConditions,
            freePeriod: null,
            subleases: null
        }
    };

function L8({
    title: t,
    body: n,
    icon: r
}) {
    const {
        t: a
    } = Gn();
    return e.jsxs(cP, {
        row: !0,
        gap: "18px",
        ycenter: !0,
        mr: "8px",
        children: [r, e.jsxs(cP, {
            children: [e.jsx(rP, {
                s: "14",
                light: !0,
                gray: !0,
                children: a(t)
            }), e.jsx(rP, {
                s: "16",
                sx: {
                    whiteSpace: "wrap"
                },
                children: n
            })]
        })]
    })
}
const k8 = ({
    tenant: t,
    sx: n,
    isReview: r = !1
}) => {
    const {
        i18n: {
            language: a
        }
    } = Gn(), i = "company" === t?.type ? h8 : p8, o = "company" === t?.type && t.name_en ? t.name_en : null;
    return e.jsxs(cP, {
        row: !0,
        gap: "18px",
        sx: {
            ...n
        },
        children: [r ? e.jsx(cP, {
            ycenter: !0,
            children: t?.photo ? e.jsx(Vp, {
                url: t?.photo?.url ?? t?.photo
            }) : e.jsx(cP, {
                sx: {
                    backgroundColor: e => u(e.palette.primary.main, .08),
                    width: "66px",
                    height: "66px",
                    display: "flex",
                    justifyContent: "center",
                    alignItems: "center",
                    borderRadius: "8px"
                },
                children: e.jsx(i, {})
            })
        }) : ("company" === t?.type || t?.photo) && e.jsx(cP, {
            ycenter: !0,
            children: e.jsx(Vp, {
                url: t?.photo?.url ?? t?.photo
            })
        }), e.jsxs(cP, {
            my: 5,
            column: !0,
            xcenter: !0,
            children: [e.jsxs(cP, {
                children: [e.jsx(rP, {
                    sx: {
                        fontSize: o ? "14px" : "16px",
                        fontWeight: o ? 400 : 600,
                        color: o ? "text.secondary" : "text.primary"
                    },
                    children: "individual" === t?.type ? t.name : "ar" === a ? t.name_ar : t.name_en
                }), o && e.jsx(rP, {
                    s: "16",
                    children: o
                })]
            }), "individual" === t?.type && t?.phone && e.jsx(rP, {
                s: "14",
                light: !0,
                color: "text.secondary",
                dir: "ltr",
                children: t?.phone
            })]
        })]
    })
};

function T8({
    title: t,
    subtitle: n,
    actionBtn: r,
    sx: a,
    cols: i,
    children: o
}) {
    return e.jsxs(cP, {
        sx: {
            borderRadius: "16px",
            border: "1px solid #E3E3E3",
            padding: "36px",
            backgroundColor: "#fff",
            ...a
        },
        children: [e.jsxs(cP, {
            row: !0,
            xbetween: !0,
            mb: o ? "24px" : "",
            alignItems: "flex-start",
            children: [e.jsxs(cP, {
                children: [e.jsx(rP, {
                    s: 24,
                    children: t
                }), e.jsx(rP, {
                    s: 16,
                    light: !0,
                    children: n
                })]
            }), r]
        }), e.jsx(cP, {
            sx: {
                display: "grid",
                gridTemplateColumns: `repeat(${i}, 1fr)`,
                rowGap: "24px "
            },
            children: o
        })]
    })
}

function j8({
    headers: t,
    rows: n
}) {
    return e.jsx(Ct, {
        sx: {
            border: "1px solid #E3E3E3",
            borderRadius: "8px"
        },
        children: e.jsx(Mt, {
            "aria-label": "customized table",
            children: e.jsxs(e.Fragment, {
                children: [e.jsx(St, {
                    sx: {
                        borderBottom: "1px solid #E3E3E3"
                    },
                    children: e.jsx(Ee, {
                        children: t.map(t => e.jsx(pP, {
                            children: e.jsx(hp, {
                                bold: !0,
                                variant: "body",
                                children: t
                            })
                        }, t))
                    })
                }), n.map(t => e.jsx(uP, {
                    sx: {
                        fontSize: "16px"
                    },
                    children: t.map(t => e.jsx(pP, {
                        sx: {
                            borderBottom: "none"
                        },
                        children: t
                    }, String(t)))
                }, String(t)))]
            })
        })
    })
}
const E8 = e => {
    if (e.type === m$.COMPANY) {
        const t = e;
        return {
            name_en: t.name_en,
            name_ar: t.name_ar,