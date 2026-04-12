                                children: e.jsx(hp, {
                                    variant: "h6",
                                    fontWeight: "600",
                                    color: "primary.main",
                                    width: "100%",
                                    textAlign: "left",
                                    children: t.title[c]
                                })
                            }), t.intro && t.intro.length > 0 && e.jsx(a, {
                                mb: 2,
                                children: t.intro.map((n, r) => e.jsx(hp, {
                                    variant: "h6",
                                    color: "text.primary",
                                    mb: r < t.intro.length - 1 ? 1 : 0,
                                    dir: d ? "rtl" : "ltr",
                                    children: n[c]
                                }, r))
                            }), t.subsectionTitle && e.jsx(a, {
                                mb: 1.5,
                                mt: t.intro && t.intro.length > 0 ? 2 : 0,
                                children: e.jsx(hp, {
                                    variant: "h6",
                                    mt: "16px",
                                    fontWeight: "600",
                                    color: "primary",
                                    dir: d ? "rtl" : "ltr",
                                    children: t.subsectionTitle[c]
                                })
                            }), t.items && t.items.length > 0 && e.jsx(ge, {
                                dense: !0,
                                sx: {
                                    p: 0
                                },
                                children: t.items.map((t, n) => e.jsx(ye, {
                                    sx: {
                                        alignItems: "flex-start",
                                        py: .25,
                                        pl: d ? 0 : 4,
                                        pr: d ? 4 : 0
                                    },
                                    children: e.jsx(Ue, {
                                        primary: e.jsx(a, {
                                            component: "span",
                                            dir: d ? "rtl" : "ltr",
                                            sx: {
                                                listStyleType: "none",
                                                display: "list-item",
                                                position: "relative",
                                                paddingInlineStart: "1.5em",
                                                "&::before": {
                                                    content: '"–"',
                                                    position: "absolute",
                                                    insetInlineStart: 0
                                                }
                                            },
                                            children: t[c]
                                        }),
                                        primaryTypographyProps: {
                                            variant: "label",
                                            color: "text.primary"
                                        }
                                    })
                                }, n))
                            }), t.closing && e.jsx(a, {
                                mt: 2,
                                children: e.jsx(hp, {
                                    variant: "h6",
                                    mt: "16px",
                                    fontWeight: "600",
                                    dir: d ? "rtl" : "ltr",
                                    children: t.closing[c]
                                })
                            }), n !== W0.sections.length - 1 && e.jsx(L, {
                                sx: {
                                    my: "16px"
                                }
                            })]
                        }, n))
                    })]
                })]
            })
        })
    };

function $0(t) {
    return e.jsx(i, {
        ...t,
        inheritViewBox: !0,
        children: e.jsx("path", {
            d: "M27.205 22.625H29.883V25.375H0.426025V22.625H3.10393V2C3.10393 1.63533 3.245 1.28559 3.4961 1.02773C3.7472 0.769866 4.08777 0.625 4.44288 0.625H17.8324C18.1875 0.625 18.5281 0.769866 18.7792 1.02773C19.0303 1.28559 19.1713 1.63533 19.1713 2V22.625H24.5271V11.625H21.8492V8.875H25.8661C26.2212 8.875 26.5618 9.01987 26.8129 9.27773C27.064 9.53559 27.205 9.88533 27.205 10.25V22.625ZM5.78183 3.375V22.625H16.4934V3.375H5.78183ZM8.45973 11.625H13.8155V14.375H8.45973V11.625ZM8.45973 6.125H13.8155V8.875H8.45973V6.125Z"
        })
    })
}

function G0(t) {
    return e.jsx(i, {
        ...t,
        inheritViewBox: !0,
        children: e.jsx("path", {
            d: "M2.10394 0.625H26.2051C26.5602 0.625 26.9007 0.769866 27.1518 1.02773C27.4029 1.28559 27.544 1.63533 27.544 2V24C27.544 24.3647 27.4029 24.7144 27.1518 24.9723C26.9007 25.2301 26.5602 25.375 26.2051 25.375H2.10394C1.74882 25.375 1.40826 25.2301 1.15715 24.9723C0.906052 24.7144 0.764984 24.3647 0.764984 24V2C0.764984 1.63533 0.906052 1.28559 1.15715 1.02773C1.40826 0.769866 1.74882 0.625 2.10394 0.625ZM3.44289 3.375V22.625H24.8661V3.375H3.44289ZM9.46817 15.75H16.8324C17.01 15.75 17.1802 15.6776 17.3058 15.5486C17.4313 15.4197 17.5019 15.2448 17.5019 15.0625C17.5019 14.8802 17.4313 14.7053 17.3058 14.5764C17.1802 14.4474 17.01 14.375 16.8324 14.375H11.4766C10.5888 14.375 9.7374 14.0128 9.10964 13.3682C8.48189 12.7235 8.12922 11.8492 8.12922 10.9375C8.12922 10.0258 8.48189 9.15148 9.10964 8.50682C9.7374 7.86216 10.5888 7.5 11.4766 7.5H12.8155V4.75H15.4934V7.5H18.8408V10.25H11.4766C11.299 10.25 11.1288 10.3224 11.0032 10.4514C10.8777 10.5803 10.8071 10.7552 10.8071 10.9375C10.8071 11.1198 10.8777 11.2947 11.0032 11.4236C11.1288 11.5526 11.299 11.625 11.4766 11.625H16.8324C17.7202 11.625 18.5716 11.9872 19.1994 12.6318C19.8271 13.2765 20.1798 14.1508 20.1798 15.0625C20.1798 15.9742 19.8271 16.8485 19.1994 17.4932C18.5716 18.1378 17.7202 18.5 16.8324 18.5H15.4934V21.25H12.8155V18.5H9.46817V15.75Z"
        })
    })
}

function K0(t) {
    return e.jsx(i, {
        ...t,
        inheritViewBox: !0,
        children: e.jsx("path", {
            d: "M24.7051 8.5V26.3654C24.7063 26.5459 24.6729 26.725 24.6067 26.8923C24.5406 27.0596 24.443 27.2119 24.3195 27.3405C24.1961 27.4691 24.0492 27.5714 23.8872 27.6417C23.7252 27.7119 23.5513 27.7487 23.3755 27.75H1.93352C1.58113 27.75 1.24315 27.6063 0.993841 27.3506C0.744535 27.0948 0.604298 26.7479 0.603943 26.386V1.614C0.603943 0.875625 1.20513 0.25 1.94557 0.25H16.6673L24.7051 8.5ZM22.0272 9.875H15.3324V3H3.28185V25H22.0272V9.875ZM7.2987 7.125H11.3156V9.875H7.2987V7.125ZM7.2987 12.625H18.0103V15.375H7.2987V12.625ZM7.2987 18.125H18.0103V20.875H7.2987V18.125Z"
        })
    })
}
const Q0 = () => e.jsx(lP, {
        center: !0,
        column: !0,
        md: 4,
        xs: 6,
        sx: {
            margin: "1rem 0 ",
            textAlign: "center",
            "&:hover": {
                color: "primary.main"
            }
        },
        children: e.jsx(Ne, {
            sx: {
                width: "100%",
                height: "144px"
            },
            children: e.jsxs(et, {
                sx: {
                    paddingBottom: "16px !important",
                    height: "100%",
                    display: "flex",
                    alignItems: "center",
                    justifyContent: "center",
                    flexDirection: "column"
                },
                children: [e.jsx(Fe, {
                    variant: "circular",
                    width: 50,
                    height: 50
                }), e.jsx(Fe, {
                    variant: "text",
                    width: "70%",
                    height: 30,
                    sx: {
                        mt: 1
                    }
                })]
            })
        })
    }),
    J0 = () => e.jsx(Ne, {
        sx: {
            p: "8px 8px",
            height: "100%"
        },
        children: e.jsx(ap, {
            sx: {
                width: "100%",
                height: "100%",
                display: "flex",
                flexDirection: "column",
                justifyContent: "center"
            },
            children: e.jsxs(sP, {
                children: [e.jsx(lP, {
                    xs: 6,
                    sx: {
                        display: "flex",
                        justifyContent: "center"
                    },
                    children: e.jsxs(ap, {
                        sx: {
                            position: "relative",
                            width: 200,
                            height: 200,
                            display: "flex",
                            justifyContent: "center",
                            alignItems: "center"
                        },
                        children: [e.jsx(Fe, {
                            variant: "circular",
                            width: 200,
                            height: 200,
                            animation: "wave",
                            sx: {
                                borderRadius: "50%",
                                transform: "none",
                                "&::after": {
                                    animation: "wave 1.6s linear 0.5s infinite",
                                    background: "linear-gradient(90deg, transparent, rgba(0, 0, 0, 0.04), transparent)"
                                }
                            }
                        }), e.jsx(ap, {
                            sx: {
                                position: "absolute",
                                width: 140,
                                height: 140,
                                borderRadius: "50%",
                                backgroundColor: "white"
                            }
                        })]
                    })
                }), e.jsxs(lP, {
                    xs: 5,
                    lg: 6,
                    xl: 4,
                    sx: {
                        display: "flex",
                        flexDirection: "column",
                        justifyContent: "center",
                        pr: 12
                    },
                    children: [e.jsx(Fe, {
                        variant: "text",
                        width: "100%",
                        height: 30
                    }), e.jsx(Fe, {
                        variant: "text",
                        width: "100%",
                        height: 30
                    }), e.jsx(Fe, {
                        variant: "text",
                        width: "100%",
                        height: 30
                    }), e.jsx(Fe, {
                        variant: "text",
                        width: "100%",
                        height: 30
                    })]
                })]
            })
        })
    }),
    X0 = () => e.jsxs(Ne, {
        sx: {
            p: "12px 8px"
        },
        children: [e.jsx(ct, {
            sx: {
                px: 0
            },
            title: e.jsxs(e.Fragment, {
                children: [e.jsx(ap, {
                    display: "flex",
                    alignItems: "center",
                    justifyContent: "space-between",
                    children: e.jsx(Fe, {
                        width: "200px",
                        height: 28,
                        sx: {
                            ml: "16px",
                            mb: "12px"
                        }
                    })
                }), e.jsx(L, {})]
            })
        }), e.jsx(et, {
            sx: {
                display: "flex"
            },
            children: e.jsxs(ap, {
                sx: {
                    width: "100%"
                },
                children: [e.jsx(ap, {
                    sx: {
                        height: "250px"
                    },
                    children: e.jsx(Fe, {
                        variant: "rectangular",
                        height: 250
                    })
                }), e.jsx(ap, {
                    sx: {
                        mt: "24px"
                    },
                    children: e.jsx(ap, {
                        display: "flex",
                        alignItems: "center",
                        flexWrap: "wrap",
                        children: [...Array(3)].map((t, n) => e.jsxs(ap, {
                            display: "flex",
                            alignItems: "center",
                            mr: 4,
                            mb: 1,
                            children: [e.jsx(Fe, {
                                variant: "rectangular",
                                width: 18,
                                height: 8,
                                sx: {
                                    borderRadius: "8px",
                                    mr: 1
                                }
                            }), e.jsx(Fe, {
                                width: 100,
                                height: 20
                            })]
                        }, n))
                    })
                })]
            })
        })]
    }),
    e2 = () => e.jsxs(Ne, {
        sx: {
            p: "12px 8px"
        },
        children: [e.jsx(ct, {
            sx: {
                px: 0
            },
            title: e.jsxs(e.Fragment, {
                children: [e.jsx(ap, {
                    display: "flex",
                    alignItems: "center",
                    justifyContent: "space-between",
                    children: e.jsx(Fe, {
                        width: "280px",
                        height: 44,
                        sx: {
                            ml: "16px",
                            mb: "12px"
                        }
                    })
                }), e.jsx(L, {})]
            })
        }), e.jsx(et, {
            children: [...Array(3)].map((t, n) => e.jsx(Fe, {
                width: "100%",
                height: 80
            }))
        })]
    }),
    t2 = ({
        item: t
    }) => {
        const {
            t: n
        } = Gn();
        return e.jsx(lP, {
            center: !0,
            column: !0,
            xs: 4,
            sx: {
                opacity: t?.clickHandler ? 1 : .5,
                width: "100%",
                height: "100%",
                margin: "1rem 0 ",
                textAlign: "center",
                "&:hover": {
                    color: "primary.main"
                }
            },
            children: e.jsx(Ne, {
                sx: {
                    width: "100%",
                    height: "144px",
                    cursor: "pointer",
                    transition: " 0.3s all ease ",
                    "&:hover": {
                        background: "#33333408"
                    }
                },
                onClick: t?.clickHandler,
                children: e.jsxs(et, {
                    sx: {
                        paddingBottom: "16px !important",
                        height: "100%",
                        display: "flex",
                        alignItems: "center",
                        justifyContent: "center",
                        flexDirection: "column"
                    },
                    children: [e.jsx($e, {
                        badgeContent: t?.badgeCount || 0,
                        color: "primary",
                        sx: {
                            cursor: "pointer",
                            fontSize: "10px",
                            "& .MuiBadge-badge": {
                                color: "white"
                            }
                        },
                        overlap: "circular",
                        anchorOrigin: {
                            vertical: "top",
                            horizontal: "right"
                        },
                        max: 999,
                        showZero: !0,
                        children: e.jsx(wp, {
                            sx: {
                                minWidth: "50px",
                                height: "50px"
                            },
                            children: t?.Icon
                        })
                    }), e.jsx(hp, {
                        variant: "caption",
                        sx: {
                            lineHeight: "17px"
                        },
                        children: n(t?.title)
                    })]
                })
            })
        })
    };

function n2() {
    const t = ii(),
        {
            t: n
        } = Gn(),
        r = Ft(),
        {
            data: a,
            isLoading: i
        } = tl(["DASHBOARD", "REQUIRES_ATTENTION"], async () => await (async () => (await lo("/api-management/dashboard/requires-attention")).data)()),
        o = [{
            clickHandler: () => r("/requests?type=1&filter[rf_status_id][0][id]=1&filter[rf_status_id][0][name]=New"),
            Icon: e.jsx(K0, {
                sx: {
                    height: "30px",
                    width: "30px"
                }
            }),
            title: "dashboard.requiresAttention.pending_home_requests",
            badgeCount: a?.requests_approval,
            enabled: t.can(qI.View, $I.HomeServices)
        }, {
            clickHandler: () => r("/requests?type=2&filter[rf_status_id][0][id]=1&filter[rf_status_id][0][name]=New"),
            Icon: e.jsx($0, {
                sx: {
                    height: "30px",
                    width: "30px"
                }
            }),
            title: "dashboard.requiresAttention.Pending_common_requests",
            badgeCount: a?.pending_complaints,
            enabled: t.can(qI.View, $I.NeighbourhoodServices)
        }, {
            clickHandler: () => r("/transactions/overdues"),
            Icon: e.jsx(G0, {
                sx: {
                    height: "30px",
                    width: "30px"
                }
            }),
            title: "dashboard.requiresAttention.overdues",
            badgeCount: a?.overdue_recipes,
            enabled: !0
        }];
    return e.jsxs(e.Fragment, {
        children: [e.jsx(hp, {
            variant: "h6",
            sx: {
                py: "10px"
            },
            children: n("dashboard.requiresAttention.title")
        }), e.jsx(sP, {
            spacing: 8,
            children: e.jsx(sp, {
                condition: !i,
                fallback: e.jsxs(e.Fragment, {
                    children: [e.jsx(Q0, {}), e.jsx(Q0, {}), e.jsx(Q0, {})]
                }),
                children: o.filter(e => e.enabled).map(t => e.jsx(t2, {
                    item: t
                }))
            })
        })]
    })
}
const r2 = "/assets/svg/annconcementBg-CoiGzHhU.svg",
    a2 = ({
        title: t,
        onClick: n = () => {}
    }) => {
        const {
            i18n: r
        } = Gn();
        return e.jsxs(dP, {
            fullWidth: !0,
            onClick: n,
            sx: {
                justifyContent: "space-between",
                mb: "2px"
            },
            children: [e.jsx(cP, {
                row: !0,
                children: e.jsx(rP, {
                    variant: "h6",
                    s: 16,
                    sx: {
                        display: "flex",
                        alignItems: "center",
                        textAlign: "start"
                    },
                    children: t
                })
            }), e.jsx(w, {
                "aria-label": "Click left",
                color: "primary",
                sx: {
                    background: "none"
                },
                children: "rtl" === r.dir() ? e.jsx(DI, {
                    fontSize: "small",
                    sx: {
                        color: e => e.palette.primary.primaryDark
                    }
                }) : e.jsx(kI, {
                    fontSize: "small",
                    sx: {
                        color: e => e.palette.primary.primaryDark
                    }
                })
            })]
        })
    };
var i2, o2 = {};

function s2() {
    if (i2) return o2;
    i2 = 1;
    var e = h();
    Object.defineProperty(o2, "__esModule", {
        value: !0
    }), o2.default = void 0;
    var t = e(jp()),
        n = m();
    return o2.default = (0, t.default)((0, n.jsx)("path", {
        d: "M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2m5 11h-4v4h-2v-4H7v-2h4V7h2v4h4z"
    }), "AddCircle"), o2
}
const l2 = It(s2()),
    d2 = e => {
        if (!e) return "";
        const t = e.split(":");
        return 2 === t.length ? e : 1 === t.length ? `${e}:00` : t.length > 2 ? t.slice(0, 2).join(":") : void 0
    },
    c2 = e => Array.isArray(e) ? e.map(e => "object" == typeof e && "id" in e ? e.id : Number(e)) : [],
    u2 = async e => {
        try {
            const n = await lo(`/api-management/rf/announcements/${e}`);
            return {
                id: (t = n.data).id,
                title: t.title,
                location: t.location,
                start_date: t.start_date,
                end_date: t.end_date,
                start_time: t.start_time,
                end_time: t.end_time,
                images: t.images,
                notify: t.notify,
                notified_users: t.notified_users,
                maps: t.maps,
                description: t.description,
                details: t.description,
                created_at: t.created_at
            }
        } catch (n) {
            throw n
        }
        var t
    }, p2 = async e => await po(`/api-management/rf/announcements/${e?.id}`), h2 = async (e, t) => {
        try {
            const n = (e => {
                const t = {
                    description: e.details,
                    end_date: Fj(e.end_date).format("YYYY-MM-DD"),
                    start_date: Fj(e.start_date).format("YYYY-MM-DD"),
                    title: e.title,
                    end_time: d2(e.end_time),
                    start_time: d2(e.start_time),
                    notify_user_type: e.notify,
                    notify: e.notify,
                    notified_users: e.notified_users,
                    notify_users: [],
                    maps: e.location,
                    location: e.location?.formattedAddress,
                    is_visible: "1"
                };
                return 1 != e.notify && (delete t.notified_users, t.notify_users = c2(e.notified_users)), t
            })(e);
            return (t ? await uo(`/api-management/rf/announcements/${t}`, n) : await co("/api-management/rf/announcements", n)).data
        } catch (n) {
            throw n
        }
    };

function m2(e) {
    const {
        data: t,
        isLoading: n
    } = tl([KI, e], async () => await (async e => {
        try {
            return (await lo("/api-management/rf/announcements", {
                type: e
            })).data
        } catch (t) {
            throw t
        }
    })(e));
    return {
        data: t,
        isLoading: n
    }
}
var f2, g2 = {};

function y2() {
    if (f2) return g2;
    f2 = 1;
    var e = h();
    Object.defineProperty(g2, "__esModule", {
        value: !0
    }), g2.default = void 0;
    var t = e(jp()),
        n = m();
    return g2.default = (0, t.default)([(0, n.jsx)("path", {
        d: "M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7M7 9c0-2.76 2.24-5 5-5s5 2.24 5 5c0 2.88-2.88 7.19-5 9.88C9.92 16.21 7 11.85 7 9"
    }, "0"), (0, n.jsx)("circle", {
        cx: "12",
        cy: "9",
        r: "2.5"
    }, "1")], "LocationOnOutlined"), g2
}
const v2 = It(y2()),
    _2 = ({
        item: t
    }) => {
        const {
            t: n
        } = Gn(), {
            CurrentBrand: r
        } = Gc(), a = qc[r]?.loadingLogo;
        return e.jsx(et, {
            sx: {
                minHeight: "100%",
                display: "flex",
                width: "100%",
                p: "22px",
                pb: "0 !important"
            },
            children: e.jsxs(ap, {
                sx: {
                    display: "flex",
                    mb: "22px"
                },
                children: [e.jsx(BI, {
                    src: t?.images[0]?.url ?? a,
                    alt: "",
                    sx: {
                        width: {
                            xs: "100%",
                            sm: "100%",
                            md: "100%",
                            lg: "122px"
                        },
                        height: {
                            xs: "100%",
                            sm: "100%",
                            md: "auto",
                            lg: "auto"
                        },
                        borderRadius: "8px",
                        maxHeight: {
                            xs: "112px",
                            sm: "fit-content",
                            md: "fit-content",
                            lg: "130px"
                        },
                        mr: {
                            xs: 0,
                            sm: 0,
                            md: 0,
                            lg: "16px"
                        },
                        mb: {
                            xs: "16px",
                            sm: "16px",
                            md: 4,
                            lg: 0
                        },
                        display: "inline-block",
                        objectFit: "contain"
                    }
                }), e.jsxs(ap, {
                    sx: {
                        maxWidth: "auto"
                    },
                    children: [e.jsx(hp, {
                        variant: "label",
                        s: 18,
                        sx: {
                            my: 2
                        },
                        children: t?.title
                    }), e.jsxs(ap, {
                        row: !0,
                        ycenter: !0,
                        sx: {
                            mt: "16px"
                        },
                        children: [e.jsx(v2, {
                            sx: {
                                fontSize: "2.2rem",
                                color: "#969798",
                                mr: "5px"
                            }
                        }), e.jsxs(ap, {
                            children: [e.jsx(hp, {
                                variant: "body",
                                s: 10,
                                sx: {
                                    fontWeight: 400,
                                    color: "#525451"
                                },
                                children: n("location")
                            }), e.jsx(hp, {
                                s: 14,
                                sx: {
                                    fontWeight: 700,
                                    maxWidth: "300px",
                                    display: "-webkit-box",
                                    WebkitLineClamp: 2,
                                    WebkitBoxOrient: "vertical",
                                    overflow: "hidden",
                                    textOverflow: "ellipsis"
                                },
                                children: t?.location || t?.maps?.formattedAddress
                            })]
                        })]
                    })]
                })]
            })
        })
    };

function x2() {
    const [t] = Dt.useState(0), {
        data: n,
        isLoading: r
    } = m2("upcoming"), a = Ft(), {
        t: i
    } = Gn();
    return r ? e.jsxs(e.Fragment, {
        children: [e.jsx(a2, {
            title: i("announcements.Announcements"),
            onClick: () => a("/dashboard/announcements")
        }), e.jsx(Ne, {
            sx: {
                display: "flex",
                alignItems: "center",
                justifyContent: "center"
            },
            elevation: 0,
            children: e.jsx(et, {
                sx: {
                    width: "100%",
                    textAlign: "center",
                    my: 5
                },
                children: e.jsxs(ap, {
                    sx: {
                        display: "flex",
                        alignItems: "center",
                        gap: 10
                    },
                    children: [e.jsx(Fe, {
                        variant: "circular",
                        width: 120,
                        height: 75
                    }), e.jsxs(ap, {
                        sx: {
                            width: "100%"
                        },
                        children: [e.jsx(Fe, {
                            variant: "text",
                            width: "100%",
                            height: 30
                        }), e.jsx(Fe, {
                            variant: "text",
                            width: "100%",
                            height: 50
                        })]
                    })]
                })
            })
        })]
    }) : e.jsxs(e.Fragment, {
        children: [e.jsx(a2, {
            title: i("announcements.Announcements"),
            onClick: () => a("/dashboard/announcements")
        }), 0 === n?.length ? e.jsx(Ne, {
            sx: {
                display: "flex",
                alignItems: "center",
                justifyContent: "center",
                background: `url(${r2})`,
                backgroundRepeat: "no-repeat",
                backgroundSize: "cover",
                cursor: "pointer"
            },
            onClick: () => {
                a("/dashboard/announcements")
            },
            elevation: 0,
            children: e.jsxs(et, {
                sx: {
                    textAlign: "center",
                    my: 5
                },
                children: [e.jsx(l2, {
                    style: {
                        fontSize: 60
                    },
                    color: "primary"
                }), e.jsx(hp, {
                    variant: "h6",
                    children: i("dashboard.create_announcement")
                })]
            })
        }) : e.jsx(Ne, {
            onClick: () => a(`/dashboard/announcements/${n?.[t]?.id}`, {
                state: {
                    announcement: n?.[t]
                }
            }),
            sx: {
                height: "auto",
                flex: 1,
                position: "relative",
                overflow: "inherit",
                cursor: "pointer",
                paddingBottom: "0px",
                transition: " 0.3s all ease ",
                "&:hover": {
                    background: "#33333408"
                }
            },
            children: e.jsx(_2, {
                item: n?.[t]
            })
        })]
    })
}
const b2 = Dt.lazy(() => SZ(() => rr(() => import("./UnitsChart-DMljoc6u.js"), __vite__mapDeps([87, 1, 2, 3, 88, 89, 90, 6])))),
    w2 = Dt.lazy(() => SZ(() => rr(() => import("./RequestsChart-BUH15o2i.js"), __vite__mapDeps([91, 1, 2, 3, 88, 89, 90, 6])))),
    C2 = Dt.lazy(() => SZ(() => rr(() => import("./OffersRewardsChart-CvPE6VvC.js"), __vite__mapDeps([92, 1, 2, 3, 88, 93, 90, 6])))),
    M2 = Dt.lazy(() => SZ(() => rr(() => import("./TopOffers-DzQVbInA.js"), __vite__mapDeps([94, 1, 2, 3, 93, 90, 6]))));

function S2() {
    const {
        planFeatures: t
    } = Qc(), n = Ht(), r = ii(), [a, i] = Dt.useState(!0), o = "All Time", s = t?.ENABLE_SEND_ANNOUNCEMENT && r?.can(qI.View, $I.Announcements), l = t?.ENABLE_OFFERS && r?.can(qI.View, $I.Offers), d = !1 === n?.state?.state?.private_policy_accepted;
    return Dt.useEffect(() => {
        localStorage.removeItem(qO)
    }, []), e.jsx(ap, {
        sx: {
            maxWidth: "100%",
            px: {
                xs: 2,
                sm: 3,
                md: 0
            },
            overflowX: "hidden"
        },
        children: e.jsxs(sP, {
            sx: {
                maxWidth: "1920px",
                pr: {
                    xs: 0,
                    sm: 0,
                    md: "40px"
                },
                pb: {
                    xs: 4,
                    sm: 6,
                    md: 8,
                    lg: 10
                }
            },
            justifyContent: "flex-start",
            spacing: {
                xs: 8,
                md: 10,
                lg: 14
            },
            children: [d && e.jsx(U0, {
                isOpen: a,
                handleClose: () => {
                    i(!1)
                },
                user_id: n?.state?.state?.user_id
            }), e.jsx(q0, {}), e.jsx(lP, {
                xs: 12,
                sm: 12,
                md: 4,
                lg: 4,
                xl: 4,
                sx: {
                    my: 1,
                    display: s ? "block" : "none"
                },
                children: e.jsx(x2, {})
            }), t?.ENABLE_REQUIRE_ATTENTION && e.jsx(lP, {
                xs: 12,
                sm: 12,
                md: 8.5,
                lg: 7.8,
                xl: 8,
                sx: {
                    my: 1
                },
                children: e.jsx(n2, {})
            }), e.jsx(lP, {
                xs: 12,
                sm: 12,
                md: 12,
                lg: 6,
                xl: 4,
                children: e.jsx(b2, {
                    selectedPeriod: o
                })
            }), t?.ENABLE_SERVICES_SETTINGS && e.jsx(oi, {
                I: qI.Update,
                this: $I.HomeServices,
                children: e.jsx(lP, {
                    xs: 12,
                    sm: 12,
                    md: 12,
                    lg: 6,
                    xl: 4,
                    children: e.jsx(w2, {
                        selectedPeriod: o
                    })
                })
            }), t?.ENABLE_OFFERS && e.jsx(lP, {
                xs: 12,
                sm: 12,
                md: 12,
                lg: 12,
                xl: 4,
                children: e.jsx(C2, {
                    selectedPeriod: o
                })
            }), l && e.jsx(lP, {
                xs: 12,
                sm: 12,
                md: 12,
                lg: 12,
                xl: 8,
                children: e.jsx(M2, {
                    selectedPeriod: o
                })
            })]
        })
    })
}

function L2() {
    const t = ii(),
        n = Ft(),
        {
            t: r
        } = Gn(),
        {
            planFeatures: a
        } = Qc(),
        o = t => e.jsx(i, {
            ...t,
            inheritViewBox: !0,
            children: e.jsx("path", {
                d: "M13.75 5H26.25V7.5H13.75V5ZM13.75 10H21.25V12.5H13.75V10ZM13.75 17.5H26.25V20H13.75V17.5ZM13.75 22.5H21.25V25H13.75V22.5ZM3.75 5H11.25V12.5H3.75V5ZM6.25 7.5V10H8.75V7.5H6.25ZM3.75 17.5H11.25V25H3.75V17.5ZM6.25 20V22.5H8.75V20H6.25Z"
            })
        }),
        s = t => e.jsx(i, {
            ...t,
            inheritViewBox: !0,
            children: e.jsx("path", {
                d: "M21.25 10.5V3H25C25.3315 3 25.6495 3.1317 25.8839 3.36612C26.1183 3.60054 26.25 3.91848 26.25 4.25V9.25C26.25 9.58152 26.1183 9.89946 25.8839 10.1339C25.6495 10.3683 25.3315 10.5 25 10.5H21.25ZM18.75 28C18.75 28.3315 18.6183 28.6495 18.3839 28.8839C18.1495 29.1183 17.8315 29.25 17.5 29.25H12.5C12.1685 29.25 11.8505 29.1183 11.6161 28.8839C11.3817 28.6495 11.25 28.3315 11.25 28V10.5H3.125V8.0925C3.12515 7.87421 3.18247 7.65976 3.29124 7.4705C3.40002 7.28124 3.55646 7.12377 3.745 7.01375L10.625 3H18.75V28Z"
            })
        }),
        l = [{
            title: "breadcrumb.lease-statement",
            icon: e.jsx(o, {
                sx: {
                    color: "#525457"
                }
            }),
            bodyList: ["reports.lease1", "reports.lease2", "reports.lease3"],
            path: "/dashboard/system-reports/Lease",
            enable: a?.ENABLE_LEASE_REPORT && t.can(qI.View, $I.LeaseStatementsReports)