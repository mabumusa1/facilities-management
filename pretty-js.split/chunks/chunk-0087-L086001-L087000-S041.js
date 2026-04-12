            data: o,
            fetchNextPage: s,
            hasNextPage: l,
            isFetching: d,
            isLoading: c
        } = al({
            queryKey: [SF],
            queryFn: ({
                pageParam: e = 1
            }) => (async ({
                page: e,
                limit: t
            }) => {
                const n = await lo(`/api-management/notifications?per_page=${t}&page=${e}`);
                return n?.data
            })({
                page: e,
                limit: t
            }),
            getNextPageParam: (e, t) => e.length ? t.length + 1 : void 0
        }), u = Dt.useRef(void 0), p = Dt.useCallback(e => {
            c || (u.current && u.current.disconnect(), u.current = new IntersectionObserver(e => {
                e[0].isIntersecting && l && !d && s()
            }), e && u.current.observe(e))
        }, [s, l, d, c]);
        if (c) return e.jsx(hP, {});
        const h = o?.pages?.reduce((e, t) => [...e, ...t], []),
            m = (new Date).toDateString(),
            f = new Date((new Date).setDate((new Date).getDate() - 1)).toDateString(),
            g = h?.reduce((e, t) => {
                const n = new Date(t.created_at).toDateString(),
                    r = n === m ? "today" : n === f ? "yesterday" : "otherDates";
                return e[r] = e[r] || [], e[r].push(t), e
            }, {
                today: [],
                yesterday: [],
                otherDates: []
            });
        return e.jsxs(e.Fragment, {
            children: [e.jsx(cP, {
                children: e.jsx(dP, {
                    variant: "outlined",
                    onClick: async () => {
                        i(!0), await (async () => {
                            const e = await uo("/api-management/notifications/mark-all-as-read");
                            return e?.data
                        })(), i(!1), r.invalidateQueries([SF]), r.invalidateQueries([LF])
                    },
                    disabled: a || !h || 0 === h?.length,
                    className: "w-full",
                    sx: {
                        position: "absolute",
                        right: "4rem",
                        top: "4rem"
                    },
                    children: n("allUnread")
                })
            }), h && h?.length ? Object.keys(g).map(t => e.jsxs(cP, {
                ml: "1rem",
                children: [g[t].length > 0 && e.jsx(rP, {
                    s: "14",
                    mt: "2rem",
                    mb: "0.7rem",
                    children: ["today", "yesterday"].includes(t) ? n(t) : tR(g[t][0].created_at).format("DD/MM/YYYY")
                }), g[t]?.map(t => e.jsx(dU, {
                    notificationItem: t,
                    ref: p
                }, t.id))]
            }, t)) : e.jsxs(cP, {
                sx: {
                    textAlign: "center",
                    my: "auto",
                    height: "60vh"
                },
                ycenter: !0,
                xcenter: !0,
                column: !0,
                "data-testid": "no-notifications",
                children: [e.jsx(nN, {
                    sx: {
                        width: "66px",
                        height: "70px"
                    }
                }), e.jsx(rP, {
                    s: "36",
                    children: n("noNotifications")
                }), e.jsx(rP, {
                    s: "16",
                    light: !0,
                    children: n("noNotifications_subtitle")
                })]
            }), d && e.jsx(cP, {
                sx: {
                    textAlign: "center",
                    my: "1rem"
                },
                children: e.jsx(rP, {
                    s: "16",
                    light: !0,
                    center: !0,
                    mx: "auto",
                    children: n("loadingMore")
                })
            })]
        })
    },
    Y2 = {
        primaryLight: {
            light: "#008EA5",
            main: "#008EA5",
            primary: "#008EA5",
            dark: "#008EA5",
            contrastText: "#fff",
            buttonText: "#fff",
            buttonText2: "#ffffff",
            grey: "#525451",
            text1: "#232425",
            textSecondary: "#525451",
            primaryDark: "#004256",
            secondary: "#F09F42"
        },
        navyprimary: {
            light: "#fff",
            main: "#fff",
            primary: "#fff",
            dark: "#008EA5",
            contrastText: "#fff",
            buttonText: "#004256",
            buttonText2: "#008EA5",
            grey: "rgba(255, 255, 255, 0.84)",
            text1: "#fff",
            primaryDark: "#fff",
            secondary: "#F09F42"
        },
        secondary: {
            light: "#F09F42",
            main: "#F09F42",
            dark: "#EFFCF6",
            contrastText: "#000",
            buttonText: "#004256"
        },
        grey: {
            light: "#E3E3E3",
            main: "rgba(255, 255, 255, 0.84)",
            dark: "#ef6c00",
            contrastText: "rgba(0, 0, 0, 0.87)",
            grey900: "#2E3032"
        },
        primary: {
            grey: "#525451",
            text1: "#232425"
        }
    },
    B2 = r({
        typography: {
            h1: {
                fontSize: 36,
                fontWeight: 700
            },
            h2: {
                fontSize: 24,
                fontWeight: 700
            },
            h3: {
                fontSize: 18,
                fontWeight: 700
            },
            h4: {
                fontSize: 16,
                fontWeight: 700
            },
            h5: {
                fontSize: 14,
                fontWeight: 700
            },
            body1: {
                fontSize: 16
            },
            body2: {
                fontSize: 12,
                fontWeight: 400
            },
            subtitle1: {
                fontSize: 14,
                color: "#B6B6B6"
            },
            subtitle2: {
                fontSize: 12,
                color: "#B6B6B6"
            }
        }
    }),
    z2 = n(r({
        components: {
            MuiTypography: {
                variants: [{
                    props: {
                        variant: "hd56"
                    },
                    style: {
                        fontSize: "56px",
                        fontWeight: "bold",
                        [B2.breakpoints.down("xl")]: {
                            fontSize: "32px"
                        },
                        [B2.breakpoints.down("md")]: {
                            fontSize: "24px"
                        }
                    }
                }, {
                    props: {
                        variant: "hd52"
                    },
                    style: {
                        fontSize: "52px",
                        fontWeight: "bold",
                        lineHeight: "58px",
                        [B2.breakpoints.down("xl")]: {
                            fontSize: "32px",
                            lineHeight: "40px"
                        },
                        [B2.breakpoints.down("md")]: {
                            fontSize: "24px"
                        }
                    }
                }, {
                    props: {
                        variant: "hd32"
                    },
                    style: {
                        fontSize: "32px",
                        fontWeight: "bold",
                        lineHeight: "36px",
                        [B2.breakpoints.down("xl")]: {
                            fontSize: "24px",
                            lineHeight: "27px"
                        },
                        [B2.breakpoints.down("md")]: {
                            lineHeight: "18px",
                            fontSize: "16px"
                        }
                    }
                }, {
                    props: {
                        variant: "hd32"
                    },
                    style: {
                        fontSize: "32px",
                        fontWeight: "bold",
                        lineHeight: "36px",
                        [B2.breakpoints.down("xl")]: {
                            fontSize: "24px",
                            lineHeight: "27px"
                        },
                        [B2.breakpoints.down("md")]: {
                            lineHeight: "18px",
                            fontSize: "16px"
                        }
                    }
                }, {
                    props: {
                        variant: "hd34"
                    },
                    style: {
                        fontSize: "34px",
                        [B2.breakpoints.down("xl")]: {
                            fontSize: "34px"
                        },
                        [B2.breakpoints.down("md")]: {
                            fontSize: "18px"
                        }
                    }
                }, {
                    props: {
                        variant: "hd28"
                    },
                    style: {
                        fontSize: "28px",
                        [B2.breakpoints.down("xl")]: {
                            fontSize: "28px"
                        },
                        [B2.breakpoints.down("md")]: {
                            fontSize: "26px"
                        }
                    }
                }, {
                    props: {
                        variant: "b20"
                    },
                    style: {
                        fontSize: "20px",
                        [B2.breakpoints.down("xl")]: {
                            fontSize: "16px"
                        }
                    }
                }, {
                    props: {
                        variant: "body16"
                    },
                    style: {
                        fontSize: "16px"
                    }
                }, {
                    props: {
                        variant: "hd44"
                    },
                    style: {
                        fontSize: "44px",
                        fontWeight: "bold",
                        lineHeight: "49px",
                        [B2.breakpoints.down("xl")]: {
                            fontSize: "24px",
                            lineHeight: "27px"
                        }
                    }
                }, {
                    props: {
                        variant: "body1"
                    },
                    style: {
                        fontSize: "36px",
                        color: Y2.grey.main
                    }
                }, {
                    props: {
                        variant: "body24"
                    },
                    style: {
                        fontSize: "24px",
                        [B2.breakpoints.down("xl")]: {
                            fontSize: "16px"
                        },
                        [B2.breakpoints.down("md")]: {
                            fontSize: "16px"
                        }
                    }
                }]
            },
            MuiDialog: {
                styleOverrides: {
                    root: {
                        "& .MuiPaper-root": {
                            borderRadius: "20px"
                        }
                    }
                }
            },
            MuiCard: {
                styleOverrides: {
                    root: {
                        boxShadow: "0px 0px 21px rgba(218, 218, 218, 0.3)",
                        borderRadius: "12px"
                    }
                }
            },
            MuiFormControl: {
                styleOverrides: {
                    root: {
                        "& .MuiOutlinedInput-input": {
                            padding: "12.5px 14px"
                        },
                        "& .MuiInputLabel-root": {
                            height: "100%",
                            color: "#A0AEC0"
                        },
                        "& .MuiOutlinedInput-root": {
                            "& fieldset": {
                                borderColor: "#E2E8F0"
                            }
                        },
                        "& .MuiInputBase-root": {
                            borderRadius: "10px",
                            border: "1px solid #E2E8F0 !important"
                        }
                    }
                }
            },
            MuiCssBaseline: {
                styleOverrides: {
                    "@global": {
                        a: {
                            textDecoration: "none"
                        }
                    }
                }
            }
        }
    })),
    U2 = {
        navy: n(r(z2, {
            palette: {
                primary: Y2.navyprimary,
                secondary: Y2.secondary,
                grey: Y2.grey
            },
            components: {
                MuiTypography: {
                    styleOverrides: {
                        root: {
                            color: Y2.navyprimary.main
                        }
                    }
                },
                MuiButton: {
                    styleOverrides: {
                        containedPrimary: {
                            color: Y2.navyprimary.buttonText,
                            "&:hover": {
                                boxShadow: "0px 0px 21px rgba(218, 218, 218, 0.3)",
                                backgroundColor: Y2.navyprimary.main
                            }
                        },
                        outlinedPrimary: {
                            color: Y2.navyprimary.main,
                            borderColor: "#fff"
                        },
                        root: {
                            borderRadius: "8px",
                            textTransform: "capitalize",
                            fontSize: "24px",
                            fontWeight: "bold",
                            padding: "16px 36px",
                            [B2.breakpoints.down("xl")]: {
                                fontSize: "16px",
                                padding: "14px 24px"
                            }
                        }
                    }
                },
                MuiIconButton: {
                    styleOverrides: {
                        root: {
                            background: "#ffffff13",
                            padding: "12px"
                        }
                    }
                }
            }
        })),
        light: n(r(z2, {
            palette: {
                primary: Y2.primaryLight,
                secondary: Y2.secondary,
                grey: Y2.secondary
            },
            components: {
                MuiButton: {
                    styleOverrides: {
                        containedPrimary: {
                            color: Y2.navyprimary.buttonText,
                            "&:hover": {
                                boxShadow: "0px 0px 21px rgba(218, 218, 218, 0.3)"
                            }
                        },
                        outlinedPrimary: {
                            color: Y2.primaryLight.main
                        },
                        root: {
                            borderRadius: "8px",
                            textTransform: "capitalize",
                            fontSize: "24px",
                            fontWeight: "bold",
                            padding: "16px 36px"
                        }
                    }
                },
                MuiIconButton: {
                    styleOverrides: {
                        root: {
                            background: "#00425613",
                            padding: "12px"
                        }
                    }
                }
            }
        }))
    },
    W2 = ({
        children: t,
        theme: n
    }) => {
        const r = Dt.useMemo(() => U2[n], [n]);
        return e.jsx(Te, {
            theme: r,
            children: t
        })
    },
    Z2 = () => e.jsx(e.Fragment, {
        children: e.jsx(g, {
            container: !0,
            spacing: 2,
            children: Array.from({
                length: 9
            }, (t, n) => e.jsx(g, {
                item: !0,
                xs: 12,
                children: e.jsx(et, {
                    sx: {
                        mb: "16px",
                        boxShadow: "0px 0px 21px rgba(218, 218, 218, 0.2)",
                        backgroundColor: "white",
                        borderRadius: "8px",
                        position: "relative",
                        alignItems: "center",
                        justifyContent: "center",
                        display: "flex",
                        padding: "24px"
                    },
                    children: e.jsxs(cP, {
                        sx: {
                            display: "flex",
                            alignItems: "center",
                            gap: "20px",
                            width: "100%"
                        },
                        children: [e.jsx(Fe, {
                            variant: "circular",
                            width: 50,
                            height: 50,
                            sx: {
                                borderRadius: "50%"
                            }
                        }), e.jsxs(cP, {
                            sx: {
                                display: "flex",
                                gap: "20px",
                                flex: 1
                            },
                            children: [e.jsxs(cP, {
                                sx: {
                                    flex: 1
                                },
                                children: [e.jsx(Fe, {
                                    variant: "text",
                                    width: "60%",
                                    height: 24
                                }), e.jsx(Fe, {
                                    variant: "text",
                                    width: "80%",
                                    height: 20,
                                    sx: {
                                        mt: 1
                                    }
                                })]
                            }), e.jsxs(cP, {
                                sx: {
                                    flex: 1
                                },
                                children: [e.jsx(Fe, {
                                    variant: "text",
                                    width: "40%",
                                    height: 16
                                }), e.jsx(Fe, {
                                    variant: "text",
                                    width: "70%",
                                    height: 24,
                                    sx: {
                                        mt: 1
                                    }
                                })]
                            }), e.jsxs(cP, {
                                sx: {
                                    flex: 1
                                },
                                children: [e.jsx(Fe, {
                                    variant: "text",
                                    width: "40%",
                                    height: 16
                                }), e.jsx(Fe, {
                                    variant: "text",
                                    width: "70%",
                                    height: 24,
                                    sx: {
                                        mt: 1
                                    }
                                })]
                            }), e.jsxs(cP, {
                                sx: {
                                    flex: 1
                                },
                                children: [e.jsx(Fe, {
                                    variant: "text",
                                    width: "40%",
                                    height: 16
                                }), e.jsx(Fe, {
                                    variant: "text",
                                    width: "70%",
                                    height: 24,
                                    sx: {
                                        mt: 1
                                    }
                                })]
                            })]
                        })]
                    })
                })
            }, n))
        })
    });

function q2() {
    const {
        t: t
    } = Gn();
    return e.jsxs(cP, {
        sx: {
            display: "flex",
            flexDirection: "column",
            alignItems: "center",
            justifyContent: "center",
            height: "calc(100vh/2)",
            width: "100%",
            textAlign: "center"
        },
        children: [e.jsx(cP, {
            component: "img",
            sx: {
                width: "140px",
                display: "inline-block",
                mb: "2rem",
                filter: "grayscale(1)"
            },
            src: wI
        }), e.jsx(rP, {
            s: "28",
            sx: {
                mb: "0.7rem",
                textTransform: "capitalize"
            },
            children: t("common.NoDataAvailable")
        })]
    })
}

function $2({
    list: t,
    value: n,
    handleSelectItem: r
}) {
    const [a, i] = Dt.useState(""), {
        t: o
    } = Gn(), s = e => {
        i(e), r(e)
    };
    return Dt.useEffect(() => {
        i(n)
    }, [n]), e.jsx(e.Fragment, {
        children: t?.map((t, n) => e.jsxs(cP, {
            sx: {
                display: "flex",
                justifyContent: "space-between",
                margin: "1rem 0",
                padding: "12px",
                cursor: "pointer",
                transition: "0.3 all ease",
                borderRadius: "8px",
                "&:hover": {
                    background: "#eee"
                }
            },
            "data-testid": "people-list-item",
            onClick: () => s(t),
            children: [e.jsxs(cP, {
                sx: {
                    display: "flex",
                    alignItems: "center"
                },
                gap: "8px",
                children: [e.jsx(cP, {
                    children: e.jsx(Vp, {
                        name: t?.name
                    })
                }), e.jsxs(cP, {
                    sx: {
                        margin: "0 0.5rem"
                    },
                    children: [e.jsx(rP, {
                        s: 14,
                        children: t?.name
                    }), e.jsxs(cP, {
                        row: !0,
                        gap: "8px",
                        children: [e.jsx(rP, {
                            s: 10,
                            light: !0,
                            children: o(t?.role)
                        }), e.jsx(rP, {
                            s: 10,
                            light: !0,
                            dir: "ltr",
                            children: t?.phone_number
                        })]
                    })]
                })]
            }), e.jsx(cP, {
                children: e.jsx(C, {
                    checked: a?.id === t?.id,
                    onChange: () => s(t),
                    value: t?.id,
                    name: "radio-buttons"
                })
            })]
        }, n))
    })
}

function G2({
    people: t,
    value: n,
    handleClose: r,
    setProfID: i,
    title: s,
    handleSearch: l,
    search: d
}) {
    const [c, u] = Dt.useState(t);
    Dt.useEffect(() => {
        u(t)
    }, [t]);
    const {
        t: p
    } = Gn();
    return e.jsx(e.Fragment, {
        children: e.jsxs(a, {
            children: [e.jsxs(a, {
                component: "header",
                sx: {
                    display: "flex",
                    justifyContent: "space-between",
                    alignItems: "center"
                },
                children: [e.jsx(o, {
                    sx: {
                        "&.MuiTypography-root": {
                            fontSize: "24px"
                        }
                    },
                    children: s
                }), e.jsx(w, {
                    sx: {
                        color: "#000"
                    },
                    onClick: r,
                    children: e.jsx(ph, {})
                })]
            }), e.jsx(a, {
                sx: {
                    margin: "2rem 0"
                },
                children: l ? e.jsx(RQ, {
                    search: d,
                    handleSearch: l,
                    sx: {
                        width: "100%"
                    }
                }) : e.jsx(k, {
                    fullWidth: !0,
                    variant: "outlined",
                    children: e.jsx(T, {
                        onChange: e => (e => {
                            if (e.length > 0) {
                                const n = t.filter(t => t.name.toLowerCase().includes(e.toLowerCase()));
                                u(n)
                            } else u(t)
                        })(e.target.value),
                        value: d,
                        id: "outlined-adornment-password",
                        placeholder: p("common.search"),
                        endAdornment: e.jsx(j, {
                            position: "end",
                            children: e.jsx(yh, {
                                sx: {
                                    fontSize: "36px"
                                }
                            })
                        })
                    })
                })
            }), e.jsx(a, {
                sx: {
                    maxHeight: "410px",
                    overflowY: "scroll"
                },
                children: e.jsx($2, {
                    list: c,
                    value: n,
                    handleSelectItem: e => {
                        i(e)
                    }
                })
            })]
        })
    })
}
const K2 = ({
        id: t,
        open: n = !1,
        handleClose: r = () => {}
    }) => {
        const {
            t: a
        } = Gn(), i = Ys(), {
            reset: s
        } = bf(), [l, c] = Dt.useState(""), {
            data: u,
            isLoading: p
        } = tl(["ASSIGN", t], () => (async e => {
            try {
                return (await lo(`/api-management/rf/users/requests/professionals?rf_request_id=${e}`)).data
            } catch (t) {
                throw t
            }
        })(t), {
            enabled: !!t && n
        }), h = nl(async e => {
            await MU({
                type: vU.ASSIGNED,
                domain: e
            })
        }, {
            onSuccess: () => {
                i.refetchQueries({
                    queryKey: [hF]
                }), i.invalidateQueries([hF, t]), i.invalidateQueries([aF]), s(), Zi.success(a("common.success")), r()
            }
        });
        return e.jsx(v, {
            open: n,
            onClose: r,
            fullWidth: !0,
            maxWidth: "sm",
            children: e.jsx(_, {
                children: p ? e.jsx(ap, {
                    center: !0,
                    sx: {
                        minHeight: "200px"
                    },
                    children: e.jsx(d, {})
                }) : e.jsx(e.Fragment, {
                    children: u?.length > 0 ? e.jsxs(e.Fragment, {
                        children: [e.jsx(G2, {
                            people: u,
                            handleClose: r,
                            setProfID: e => {
                                c(e)
                            },
                            title: a("select professional"),
                            value: l
                        }), e.jsx(L, {}), e.jsx(M, {
                            sx: {
                                mt: 4,
                                p: 0
                            },
                            children: e.jsxs(ap, {
                                row: !0,
                                sx: {
                                    textAlign: "right",
                                    margin: "16px 0 0.5rem"
                                },
                                children: [e.jsx(wp, {
                                    size: "large",
                                    variant: "text",
                                    onClick: r,
                                    sx: {
                                        width: "180px"
                                    },
                                    children: a("common.cancel")
                                }), e.jsx(wp, {
                                    disabled: !l || h.isLoading,
                                    onClick: async () => h.mutate({
                                        assigneeId: +l?.id,
                                        id: +t
                                    }),
                                    variant: "contained",
                                    isLoading: h.isLoading,
                                    sx: {
                                        width: "200px"
                                    },
                                    children: a("assign")
                                })]
                            })
                        })]
                    }) : e.jsxs(e.Fragment, {
                        children: [e.jsx(o, {
                            variant: "body",
                            color: "text.secondary",
                            sx: {
                                textAlign: "center",
                                my: 24
                            },
                            children: a("requests.noDataMessage")
                        }), e.jsx(wp, {
                            fullWidth: !0,
                            component: Wt,
                            to: "/contacts",
                            children: a("requests.takeMeToContact")
                        })]
                    })
                })
            })
        })
    },
    Q2 = ({
        count: t = 4
    }) => e.jsx(ap, {
        center: !0,
        column: !0,
        sx: {
            minHeight: "200px"
        },
        children: Array.from({
            length: t
        }).map((t, n) => e.jsx(Fe, {
            variant: "rectangular",
            width: "100%",
            height: 70,
            sx: {
                marginBottom: "16px"
            }
        }, n))
    }),
    J2 = ({
        handleClose: t,
        isOpen: n,
        id: r,
        queryKey: a = []
    }) => {
        const {
            t: i
        } = Gn(), o = Ys(), [s] = $t(), [l, d] = Dt.useState(""), [c, u] = Dt.useState(null), [p, h] = Dt.useState(""), [m, f] = Dt.useState(""), [g, y] = Dt.useState(!1), v = "1" === s.get("type") ? "request_cancel_reason" : "common_area_request_cancel_reason", {
            data: _,
            isLoading: x
        } = tl({
            queryKey: ["REQUEST_CANCELLATION_REASONS", v],
            queryFn: () => (async e => (await lo("/api-management/rf/common-lists", {
                type: e.type
            })).data)({
                type: v
            })
        }), b = _?.[_?.length - 1]?.id, {
            mutate: M,
            isLoading: S
        } = nl({
            mutationFn: e => (async e => await co("/api-management/rf/requests/change-status/canceled", e))(e),
            onSuccess: () => {
                o.invalidateQueries({
                    queryKey: [hF]
                }), t()
            }
        }), T = () => {
            t(), d(""), h(""), u(null), y(!1), f("")
        };
        return e.jsx(mt, {
            open: n,
            onClose: t,
            children: e.jsx("form", {
                onSubmit: e => {
                    if (e.preventDefault(), !l) return void f(i("cancelForm.pleaseselectreason"));
                    if (c === b && !p) return y(!0), void f(i("complaint.errorDescription"));
                    M({
                        rf_request_id: r,
                        reason: c === b ? p : null,
                        reason_id: +c
                    })
                },
                children: e.jsxs(ap, {
                    column: !0,
                    sx: X2,
                    children: [e.jsx(w, {
                        onClick: T,
                        sx: {
                            position: "absolute",
                            top: "10px",
                            right: "10px"
                        },
                        children: e.jsx(ph, {})
                    }), e.jsx(ap, {
                        column: !0,
                        fullWidth: !0,
                        sx: {
                            p: "24px"
                        },
                        children: e.jsxs(ap, {
                            column: !0,
                            children: [e.jsx(hp, {
                                variant: "h4",
                                s: 24,
                                mb: "32px",
                                children: i("cancelForm.cancelTitle")
                            }), x ? e.jsx(Q2, {
                                count: 4
                            }) : e.jsxs(k, {
                                error: !!m && c !== b,
                                component: "fieldset",
                                children: [e.jsx(V, {
                                    value: l,
                                    onChange: e => {
                                        const t = e.target.value;
                                        d(t), u(Number(t) || null), f("")
                                    },