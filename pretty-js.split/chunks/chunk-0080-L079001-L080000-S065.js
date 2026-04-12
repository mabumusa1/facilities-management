                    ...e, filter: t.payload
                };
            case "STATUS":
                return {
                    ...e, status: t.payload
                };
            case "SHOWINACTIVE":
                return {
                    ...e, showInactive: t.payload
                };
            case "SELECT":
                return {
                    ...e, select: t.payload
                };
            case "SET_SELECTED":
                return {
                    ...e, selected: [...e.selected, t.payload]
                };
            case "REMOVE_SELECTED":
                return {
                    ...e, selected: [...e.selected.filter(e => e !== t.payload)]
                };
            case "RESET_SELECTED":
                return {
                    ...e, selected: []
                };
            case "SELECT_TAB":
                return {
                    ...e, selectedTab: t.payload
                };
            default:
                return e
        }
    };

function lJ() {
    const [e, t] = Dt.useReducer(sJ, oJ), {
        sort: n,
        search: r,
        page: a,
        filter: i
    } = e, {
        data: o,
        error: s,
        isError: l
    } = tl(["COMPLAINTS", n, r, a, i], async () => await (async ({
        search: e,
        page: t,
        sort: n,
        filter: r
    }) => await lo(`/api-management/new/complaints?search=${e}&page=${t}&status[]=${r}`))({
        sort: n,
        search: r,
        page: a,
        filter: i
    }));
    return l && s && Lo(s, {
        setError: s
    }, !0), {
        dispatch: t,
        handleFilter: e => {
            t({
                type: "FILTER",
                payload: Number(e)
            }), t({
                type: "PAGE",
                payload: 1
            })
        },
        filter: i,
        sort: n,
        search: r,
        page: a,
        data: o,
        total: o?.meta?.total,
        count: o?.meta?.last_page,
        handleSearch: e => {
            t({
                type: "PAGE",
                payload: 1
            }), t({
                type: "SEARCH",
                payload: e
            })
        },
        handleSort: e => {
            t({
                type: "SORT",
                payload: Number(e)
            }), t({
                type: "PAGE",
                payload: 1
            })
        }
    }
}
var dJ, cJ = {};

function uJ() {
    if (dJ) return cJ;
    dJ = 1;
    var e = h();
    Object.defineProperty(cJ, "__esModule", {
        value: !0
    }), cJ.default = void 0;
    var t = e(jp()),
        n = m();
    return cJ.default = (0, t.default)((0, n.jsx)("path", {
        d: "M3 18h6v-2H3zM3 6v2h18V6zm0 7h12v-2H3z"
    }), "Sort"), cJ
}
const pJ = It(uJ()),
    hJ = ({
        sorting: t,
        filtering: n,
        handleSort: r,
        handleFilter: a,
        filterValues: i,
        sortValues: o
    }) => {
        const {
            t: s
        } = Gn(), [l, d] = Dt.useState(s("common.Filter by")), [c, u] = Dt.useState("common.Sort"), [p, h] = Dt.useState(null), [m, f] = Dt.useState(null);
        return e.jsxs(cP, {
            row: !0,
            ycenter: !0,
            children: [n && e.jsxs(lP, {
                sx: {
                    mr: 6
                },
                children: [e.jsx(r$, {
                    onClick: e => {
                        h(e.currentTarget)
                    },
                    style: {
                        backgroundColor: "#fff",
                        minWidth: "210px",
                        color: "#232425",
                        border: "1px solid #E3E3E3 "
                    },
                    children: e.jsxs(e.Fragment, {
                        children: [e.jsx(Np, {
                            sx: {
                                color: "#232425",
                                mr: 4
                            }
                        }), l, e.jsx(zp, {})]
                    })
                }), e.jsx(tt, {
                    anchorEl: p,
                    keepMounted: !0,
                    open: Boolean(p),
                    onClose: () => {
                        h(null)
                    },
                    children: Object.entries(i).map(([t, n]) => e.jsx(H, {
                        "data-my-value": t,
                        onClick: () => (e => {
                            a(e), d(s(i[e]?.title ? i[e]?.title : i[e])), h(null)
                        })(t),
                        children: n?.color ? e.jsx(rP, {
                            variant: "caption",
                            sx: {
                                color: n?.color,
                                backgroundColor: n?.background,
                                borderRadius: 50,
                                py: 1.5,
                                px: 6,
                                fontWeight: 500
                            },
                            children: s(n.title)
                        }) : e.jsx(rP, {
                            variant: "caption",
                            sx: {
                                borderRadius: 50,
                                py: 1.5,
                                px: 6,
                                fontWeight: 500
                            },
                            children: s(n)
                        })
                    }, t))
                })]
            }), t && e.jsxs(cP, {
                sx: {
                    color: e => e.palette.primary.Greyscale900
                },
                children: [e.jsx(dP, {
                    style: {
                        backgroundColor: "#fff",
                        minWidth: "200px",
                        color: "#232425",
                        border: "1px solid #E3E3E3 "
                    },
                    color: "inherit",
                    variant: "outlined",
                    onClick: e => {
                        f(e.currentTarget)
                    },
                    children: e.jsxs(e.Fragment, {
                        children: [e.jsx(pJ, {
                            sx: {
                                mr: 4
                            }
                        }), s(c), e.jsx(zp, {})]
                    })
                }), e.jsx(tt, {
                    anchorEl: m,
                    keepMounted: !0,
                    open: Boolean(m),
                    onClose: () => {
                        f(null)
                    },
                    children: Object.entries(o).map(([t, n]) => e.jsx(H, {
                        "data-my-value": t,
                        onClick: () => (e => {
                            r(e), u(s(o[e])), f(null)
                        })(t),
                        children: s(n)
                    }, t))
                })]
            })]
        })
    },
    mJ = ({
        item: t
    }) => {
        const n = Ft(),
            r = Ys(),
            {
                t: a
            } = Gn();
        return e.jsxs(et, {
            sx: {
                minHeight: "100%",
                display: "grid"
            },
            children: [e.jsxs(sP, {
                justifyContent: "space-between",
                alignItems: "start",
                sx: {
                    mt: 2
                },
                children: [e.jsxs(lP, {
                    children: [e.jsx(o, {
                        variant: "h6",
                        sx: {
                            fontWeight: "500"
                        },
                        children: t?.unit?.name || "No Unit"
                    }), e.jsx(o, {
                        variant: "caption",
                        sx: {
                            color: Ge[600],
                            fontWeight: "400",
                            fontSize: "12px !important"
                        },
                        children: t?.date
                    })]
                }), e.jsx(lP, {
                    children: e.jsx(o, {
                        variant: "caption",
                        sx: {
                            ...iJ[t?.full_status?.value],
                            px: 4,
                            py: 2,
                            borderRadius: "5px",
                            fontWeight: "500"
                        },
                        children: a(t?.full_status?.description)
                    })
                })]
            }), e.jsx(o, {
                variant: "subtitle1",
                sx: {
                    mt: 4
                },
                children: t?.categoryComplaint?.name
            }), e.jsx(o, {
                variant: "subtitle2",
                sx: {
                    color: Ge[700],
                    fontWeight: "400",
                    mb: 6
                },
                children: t?.description
            }), e.jsxs(cP, {
                row: !0,
                children: [4 != t?.full_status?.value && 3 != t?.full_status?.value && e.jsx(e.Fragment, {
                    children: e.jsx(l, {
                        sx: {
                            mr: "10px"
                        },
                        onClick: async () => {
                            await tJ({
                                id: t.id
                            }), r.invalidateQueries(["COMPLAINTS"])
                        },
                        fullWidth: !0,
                        variant: "outlined",
                        color: "primary",
                        children: a("common.cancel")
                    })
                }), e.jsx(l, {
                    onClick: () => {
                        n(`/dashboard/issues/${t.id}/view`, {
                            state: {
                                complaint: t
                            }
                        })
                    },
                    fullWidth: !0,
                    variant: "contained",
                    color: "primary",
                    children: a("complaint.viewDetails")
                })]
            })]
        }, t.id)
    },
    fJ = ({
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
            }) => e.jsx(mJ, {
                item: t
            }),
            Header: n,
            Footer: r
        })
    };

function gJ() {
    Ft(), Ht().state;
    let {
        data: t,
        dispatch: n,
        filter: r,
        handleFilter: a,
        search: i,
        page: s,
        total: l,
        count: d,
        handleSearch: c
    } = lJ();
    const {
        t: u
    } = Gn();
    return e.jsx(fJ, {
        data: t?.data,
        footer: e.jsx(HQ, {
            page: s,
            count: d,
            handler: e => n({
                type: "PAGE",
                payload: e
            })
        }),
        title: e.jsx(FQ, {
            title: e.jsxs(e.Fragment, {
                children: [e.jsx(o, {
                    variant: "h4",
                    children: u(1 === r ? "New complaints" : 3 === r ? "Resolved" : 4 === r ? "Cancelled" : "issues.title")
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
                search: i,
                handleSearch: c
            }),
            filtering: e.jsx(hJ, {
                filtering: !0,
                handleFilter: a,
                filterValues: aJ
            }),
            actionButton: e.jsx(e.Fragment, {}),
            extraAction: e.jsx(e.Fragment, {})
        })
    })
}
const yJ = ({
        title: t,
        value: n
    }) => e.jsxs(Le, {
        maxWidth: "sm",
        sx: {
            display: "grid",
            gridTemplateColumns: "1fr 1fr",
            my: 2
        },
        children: [e.jsx(o, {
            variant: "subtitle2",
            sx: {
                color: Ge[600],
                pr: 4,
                fontWeight: "400"
            },
            children: t
        }), e.jsx(o, {
            variant: "subtitle1",
            color: "info",
            children: n
        })]
    }),
    vJ = ({
        name: t,
        phone: n,
        subtitle: r,
        showImage: a = !0,
        image: i,
        visibilities: o,
        id_number: s,
        LabelComponent: l = () => e.jsx(e.Fragment, {})
    }) => {
        const {
            t: d,
            i18n: c
        } = Gn(), u = "rtl" === c.dir();
        return e.jsx(e.Fragment, {
            children: e.jsx(sP, {
                justifyContent: "space-between",
                children: e.jsxs(lP, {
                    sx: {
                        display: "flex",
                        alignItems: "center"
                    },
                    children: [!o?.hide_resident_name && a && (i ? e.jsx(Vp, {
                        url: i || "",
                        sx: {
                            width: 45,
                            height: 45,
                            backgroundColor: "primary.main"
                        }
                    }) : e.jsx(Vp, {
                        backgroundColor: "primary.main",
                        name: t,
                        sx: {
                            backgroundColor: "primary.main"
                        }
                    })), e.jsxs(ap, {
                        sx: {
                            ml: o?.hide_resident_name ? 0 : 6
                        },
                        children: [e.jsxs(ap, {
                            row: !0,
                            gap: "6px",
                            ycenter: !0,
                            children: [!o?.hide_resident_name && e.jsx(hp, {
                                variant: "subtitle1",
                                sx: {
                                    display: "flex",
                                    alignItems: "center",
                                    fontSize: "16px !important"
                                },
                                children: t
                            }), e.jsx(l, {})]
                        }), !o?.hide_resident_number && e.jsx(hp, {
                            variant: "label",
                            sx: {
                                fontWeight: o?.hide_resident_name ? "bold" : "normal",
                                display: "flex",
                                alignItems: "center",
                                fontSize: "14px !important",
                                direction: u ? "rtl" : "ltr",
                                width: "fit-content"
                            },
                            children: n
                        }), !!r && e.jsx(hp, {
                            variant: "body",
                            sx: {
                                fontWeight: "normal",
                                display: "flex",
                                alignItems: "center",
                                fontSize: "14px !important",
                                direction: u ? "rtl" : "ltr",
                                width: "fit-content"
                            },
                            children: r
                        }), s && e.jsxs(hp, {
                            s: 14,
                            sx: {
                                display: "flex",
                                color: "primary.main"
                            },
                            children: [d("requests.idNumber"), ": ", s]
                        }), o?.hide_resident_name && e.jsx(hp, {
                            variant: "subtitle1",
                            sx: {
                                display: "flex",
                                alignItems: "center",
                                fontSize: "16px !important"
                            },
                            children: d("requests.residentContactNumb")
                        })]
                    })]
                })
            })
        })
    };
var _J = "styles-module_wrapper__1I_qj",
    xJ = "styles-module_content__2jwZj",
    bJ = "styles-module_slide__1zrfk",
    wJ = "styles-module_image__2hdkJ",
    CJ = "styles-module_close__2I1sI",
    MJ = "styles-module_navigation__1pqAE",
    SJ = "styles-module_prev__KqFRp",
    LJ = "styles-module_next__1uQwZ";
! function(e, t) {
    void 0 === t && (t = {});
    var n = t.insertAt;
    if ("undefined" != typeof document) {
        var r = document.head || document.getElementsByTagName("head")[0],
            a = document.createElement("style");
        a.type = "text/css", "top" === n && r.firstChild ? r.insertBefore(a, r.firstChild) : r.appendChild(a), a.styleSheet ? a.styleSheet.cssText = e : a.appendChild(document.createTextNode(e))
    }
}(".styles-module_wrapper__1I_qj {\n  z-index: 1;\n  display: flex;\n  align-items: center;\n  position: fixed;\n  padding: 0px 60px 0px 60px;\n  left: 0;\n  top: 0;\n  width: 100%;\n  height: 100%;\n  background-color: black;\n  box-sizing: border-box;\n}\n\n.styles-module_content__2jwZj {\n  margin: auto;\n  padding: 0;\n  width: 90%;\n  height: 100%;\n  max-height: 100%;\n  text-align: center;\n}\n\n.styles-module_slide__1zrfk {\n  height: 100%;\n  display: flex;\n  align-items: center;\n  justify-content: center;\n}\n\n.styles-module_image__2hdkJ {\n  max-height: 100%;\n  max-width: 100%;\n  user-select: none;\n  -moz-user-select: none;\n  -webkit-user-select: none;\n}\n\n.styles-module_close__2I1sI {\n  color: white;\n  position: absolute;\n  top: 15px;\n  right: 15px;\n  font-size: 40px;\n  font-weight: bold;\n  opacity: 0.2;\n  cursor: pointer;\n}\n\n.styles-module_close__2I1sI:hover {\n  opacity: 1;\n}\n\n.styles-module_navigation__1pqAE {\n  height: 80%;\n  color: white;\n  cursor: pointer;\n  position: absolute;\n  font-size: 60px;\n  line-height: 60px;\n  font-weight: bold;\n  display: flex;\n  align-items: center;\n  opacity: 0.2;\n  padding: 0 15px;\n  user-select: none;\n  -moz-user-select: none;\n  -webkit-user-select: none;\n}\n\n.styles-module_navigation__1pqAE:hover {\n  opacity: 1;\n}\n\n@media (hover: none) {\n  .styles-module_navigation__1pqAE:hover {\n    opacity: 0.2;\n  }\n}\n\n.styles-module_prev__KqFRp {\n  left: 0;\n}\n\n.styles-module_next__1uQwZ {\n  right: 0;\n}\n\n@media (max-width: 900px) {\n  .styles-module_wrapper__1I_qj {\n    padding: 0;\n  }\n}\n");
const kJ = e => {
    var t;
    const [n, r] = Dt.useState(null !== (t = e.currentIndex) && void 0 !== t ? t : 0), a = Dt.useCallback(t => {
        let a = (n + t) % e.src.length;
        a < 0 && (a = e.src.length - 1), r(a)
    }, [n]), i = Dt.useCallback(t => {
        var n;
        if (!t.target || !e.closeOnClickOutside) return;
        const r = "ReactSimpleImageViewer" === t.target.id,
            a = t.target.classList.contains("react-simple-image-viewer__slide");
        (r || a) && (t.stopPropagation(), null === (n = e.onClose) || void 0 === n || n.call(e))
    }, [e.onClose]), o = Dt.useCallback(t => {
        var n;
        "Escape" === t.key && (null === (n = e.onClose) || void 0 === n || n.call(e)), ["ArrowLeft", "h"].includes(t.key) && a(-1), ["ArrowRight", "l"].includes(t.key) && a(1)
    }, [e.onClose, a]), s = Dt.useCallback(e => {
        e.wheelDeltaY > 0 ? a(-1) : a(1)
    }, [a]);
    return Dt.useEffect(() => (document.addEventListener("keydown", o), e.disableScroll || document.addEventListener("wheel", s), () => {
        document.removeEventListener("keydown", o), e.disableScroll || document.removeEventListener("wheel", s)
    }), [o, s]), Vt.createElement("div", {
        id: "ReactSimpleImageViewer",
        className: `${_J} react-simple-image-viewer__modal`,
        onKeyDown: o,
        onClick: i,
        style: e.backgroundStyle
    }, Vt.createElement("span", {
        className: `${CJ} react-simple-image-viewer__close`,
        onClick: () => {
            var t;
            return null === (t = e.onClose) || void 0 === t ? void 0 : t.call(e)
        }
    }, e.closeComponent || "×"), e.src.length > 1 && Vt.createElement("span", {
        className: `${MJ} ${SJ} react-simple-image-viewer__previous`,
        onClick: () => a(-1)
    }, e.leftArrowComponent || "❮"), e.src.length > 1 && Vt.createElement("span", {
        className: `${MJ} ${LJ} react-simple-image-viewer__next`,
        onClick: () => a(1)
    }, e.rightArrowComponent || "❯"), Vt.createElement("div", {
        className: `${xJ} react-simple-image-viewer__modal-content`,
        onClick: i
    }, Vt.createElement("div", {
        className: `${bJ} react-simple-image-viewer__slide`
    }, Vt.createElement("img", {
        className: wJ,
        src: e.src[n],
        alt: ""
    }))))
};

function TJ({
    title: t,
    subtitle: n,
    handleClose: r
}) {
    return e.jsx(b, {
        children: e.jsxs(sP, {
            justifyContent: "space-between",
            children: [e.jsxs("div", {
                children: [e.jsx(hp, {
                    variant: "h5",
                    children: t
                }), n && e.jsx(hp, {
                    sx: {
                        fontSize: "14px !important",
                        fontWeight: 400
                    },
                    children: n
                })]
            }), e.jsx(w, {
                "aria-label": "close",
                onClick: r,
                sx: {
                    position: "absolute",
                    right: 8,
                    top: 8,
                    color: Ge[500]
                },
                children: e.jsx(ph, {})
            })]
        })
    })
}
const jJ = "RESOLVE_ISSUE";

function EJ({
    handleClose: t,
    isOpen: n,
    id: r,
    to: i
}) {
    const {
        t: o
    } = Gn(), s = Ft(), l = Ys(), d = bf(), {
        handleSubmit: c,
        formState: {
            errors: u
        },
        control: p
    } = d;
    return e.jsxs(v, {
        onClose: t,
        open: n,
        fullWidth: !0,
        maxWidth: "sm",
        children: [e.jsx(TJ, {
            title: o("issues.your feedback to your tenant"),
            handleClose: t
        }), e.jsx(a, {
            component: "form",
            onSubmit: c(async e => {
                fq(jJ, !0);
                try {
                    await (async ({
                        id: e,
                        reply: t
                    }) => await co(`/api-management/new/complaints/${e}/resolve`, {
                        reply: t
                    }))({
                        id: r,
                        reply: e.reply
                    }), Zi.success(o("common.success")), l.invalidateQueries(["COMPLAINTS"]), t(), i && s(i)
                } catch (n) {
                    fq(jJ, !1), Lo(n, {}, !0)
                }
                fq(jJ, !1)
            }),
            children: e.jsxs(_, {
                children: [e.jsx(o$, {
                    label: o("complaint.feedback"),
                    name: "reply",
                    errors: u,
                    control: p,
                    rules: {
                        required: !0
                    },
                    multiline: !0,
                    rows: 4,
                    sx: {
                        mb: 18
                    }
                }), e.jsxs(lP, {
                    sx: {
                        display: "flex",
                        justifyContent: "center",
                        alignItems: "center",
                        gap: 15
                    },
                    children: [e.jsx(r$, {
                        onClick: t,
                        variant: "outlined",
                        children: o("common.cancel")
                    }), e.jsx(a$, {
                        name: jJ,
                        variant: "contained",
                        type: "submit",
                        children: o("issues.resolve")
                    })]
                })]
            })
        })]
    })
}

function DJ() {
    const {
        t: t
    } = Gn(), n = Ft(), {
        complaint: r
    } = Ht().state || {
        complaint: []
    }, [a, i] = Dt.useState(!1), s = Ys(), [l, d] = Dt.useState(0), [c, u] = Dt.useState(!1), p = Dt.useCallback(e => {
        d(e), u(!0)
    }, []);
    return e.jsxs(Ae, {
        maxWidth: "xl",
        children: [e.jsx(IQ, {}), e.jsx(Ne, {
            sx: {
                mt: 12
            },
            children: e.jsxs(et, {
                children: [e.jsxs(sP, {
                    justifyContent: "space-between",
                    alignItems: "start",
                    sx: {
                        mt: 2
                    },
                    children: [e.jsx(lP, {
                        children: e.jsx(o, {
                            variant: "subtitle1",
                            sx: {
                                mb: 6
                            },
                            children: t("issues.details")
                        })
                    }), e.jsxs(lP, {
                        children: [e.jsx(o, {
                            variant: "caption",
                            sx: {
                                color: Ge[600],
                                pr: 4,
                                fontWeight: "400"
                            },
                            children: t("complaint.complaintStatus")
                        }), e.jsx(o, {
                            variant: "caption",
                            sx: {
                                ...iJ[r?.status?.id],
                                py: 2,
                                px: 12,
                                borderRadius: "5px",
                                fontWeight: "500",
                                textAlign: "center",
                                width: "100%",
                                textTransform: "uppercase"
                            },
                            children: t(r?.status?.name)
                        })]
                    })]
                }), e.jsx(yJ, {
                    title: t("complaint.complaintId"),
                    value: r?.id
                }), e.jsx(yJ, {
                    title: t("complaint.complaintType"),
                    value: r?.categoryComplaint?.name
                }), e.jsx(yJ, {
                    title: t("complaint.subcomplaintType"),
                    value: r?.subcategoryComplaint?.name
                }), e.jsx(yJ, {
                    title: t("complaint.unitNumber"),
                    value: r?.unit?.name
                }), e.jsx(yJ, {
                    title: t("complaint.creationDate"),
                    value: tR(Date(r?.date)).format("DD MMM, YYYY")
                })]
            })
        }), e.jsx(Ne, {
            sx: {
                my: 6
            },
            children: e.jsxs(et, {
                children: [e.jsx(o, {
                    variant: "subtitle1",
                    sx: {
                        mb: 6
                    },
                    children: t("signUp.attachments")
                }), e.jsxs(sP, {
                    children: [r?.files?.filter(e => e.url.match(/\.(jpeg|jpg|gif|png|webp)$/)).map((t, n) => e.jsx(BI, {
                        src: t.url || "",
                        onClick: () => p(n),
                        alt: "",
                        sx: {
                            width: 80,
                            height: 62,
                            objectFit: "cover",
                            borderRadius: "20%",
                            padding: "4px 10px"
                        }
                    }, n)), c && e.jsx(kJ, {
                        src: r?.files.filter(e => e.url.match(/\.(jpeg|jpg|gif|png|webp)$/)).map(e => e.url || ""),
                        currentIndex: l,
                        disableScroll: !1,
                        closeOnClickOutside: !0,
                        onClose: () => {
                            d(0), u(!1)
                        },
                        backgroundStyle: {
                            background: "rgba(0,0,0,0.5)",
                            backdropFilter: "blur(5px)"
                        }
                    })]
                })]
            })
        }), e.jsx(Ne, {
            sx: {
                my: 6
            },
            children: e.jsxs(et, {
                children: [e.jsx(o, {
                    variant: "subtitle1",
                    sx: {
                        mb: 6
                    },
                    children: t("requests.Description")
                }), e.jsx(o, {
                    variant: "subtitle2",
                    sx: {
                        fontWeight: "400"
                    },
                    children: r?.description
                })]
            })
        }), 3 === r?.status?.id && e.jsx(Ne, {
            sx: {
                my: 6
            },
            children: e.jsxs(et, {
                children: [e.jsx(o, {
                    variant: "subtitle1",
                    sx: {
                        mb: 6
                    },
                    children: t("requests.Resolved By")
                }), e.jsx(vJ, {
                    name: r?.reply_by?.name || "NA",
                    phone: r?.reply_by?.phone_number || "Not Assigned"
                }), e.jsx(o, {
                    variant: "subtitle1",
                    sx: {
                        mt: 6
                    },
                    children: t("requests.feedback")
                }), e.jsx(o, {
                    variant: "subtitle2",
                    sx: {
                        fontWeight: "400"
                    },
                    children: r?.reply
                })]
            })
        }), r?.assignee && e.jsx(Ne, {
            sx: {
                my: 6
            },
            children: e.jsxs(et, {
                children: [e.jsx(o, {
                    variant: "subtitle1",
                    sx: {
                        mb: 6
                    },
                    children: t("requests.Assigned To")
                }), e.jsx(vJ, {
                    name: r?.assignee?.name || "NA",
                    phone: r?.assignee?.phone_number || "Not Assigned"
                })]
            })
        }), e.jsx(Ne, {
            sx: {
                my: 6
            },
            children: e.jsxs(et, {
                children: [e.jsx(o, {
                    variant: "subtitle1",
                    sx: {
                        mb: 6
                    },
                    children: t("suggestions.contact details")
                }), e.jsx(vJ, {
                    name: r?.initiator?.name || "NA",
                    phone: r?.initiator?.phone_number || "Not Assigned"
                })]
            })
        }), e.jsxs(lP, {
            sx: {
                display: "flex",
                gap: "12px",
                width: "60%"
            },
            children: [e.jsx(dP, {
                sx: {
                    mr: "10px"
                },
                onClick: async () => {
                    await tJ({
                        id: r?.id
                    }), s.invalidateQueries(["COMPLAINTS"]), n("/dashboard/issues")
                },
                fullWidth: !0,
                variant: "outlined",
                color: "primary",
                children: t("common.cancel")
            }), (1 === r?.status?.id || 2 === r?.status?.id) && e.jsx(r$, {
                variant: "contained",
                onClick: () => {
                    i(!0)
                },
                sx: {
                    order: {
                        sm: "1",
                        md: "2"
                    }
                },
                children: t("issues.resolve")
            })]
        }), e.jsx(EJ, {
            handleClose: () => i(!1),
            isOpen: a,
            id: r?.id,
            to: "/dashboard/issues"
        })]
    })
}

function VJ(t) {
    const n = Ft();
    return e.jsx("div", {
        children: e.jsx("div", {
            style: {
                width: "100%",
                top: "0",
                left: "0",
                zIndex: 10,
                backgroundColor: "#f0f4f7"
            },
            children: e.jsxs("h1", {
                style: {
                    width: "50%",
                    margin: "10% auto"
                },
                children: ["Sorry, we currently do not have a web interface for tenants/professionals, please sign in through our mobile app. Thank you.", e.jsx("div", {
                    style: {
                        marginTop: "50px"
                    },
                    children: e.jsx(l, {
                        variant: "contained",
                        color: "primary",
                        onClick: () => n("/"),
                        children: "Go Home"
                    })
                })]
            })
        })
    })
}
const AJ = [{
        title: "Issues",
        path: "issues",
        element: e.jsx(gJ, {}),
        nav: !0
    }, {
        title: "Create-issue",
        path: "issues/create",
        element: e.jsx(h$, {}),
        nav: !0
    }, {
        title: "View-issue",
        path: "issues/:id/view",
        element: e.jsx(DJ, {}),
        nav: !0
    }, {
        title: "Assign-issue ",
        path: "issues/:id/assign",
        element: e.jsx(rJ, {}),
        nav: !0
    }, {
        title: "Forbidden ",
        path: "403",
        element: e.jsx(VJ, {}),
        nav: !0
    }],
    OJ = Dt.lazy(() => SZ(() => rr(() => import("./DirectoryCU-ChtYmUXh.js"), __vite__mapDeps([28, 1, 2, 3, 29, 30, 6])))),
    PJ = Dt.lazy(() => SZ(() => rr(() => import("./ViewDirectory-D7HyzTKp.js"), __vite__mapDeps([31, 1, 2, 3, 29, 6])))),
    IJ = Dt.lazy(() => SZ(() => rr(() => import("./Directory-CYzZLQK2.js"), __vite__mapDeps([32, 1, 2, 3, 30, 6])))),
    FJ = [{
        title: "Directory",
        path: "directory",
        element: e.jsx(IJ, {}),
        nav: !0
    }, {
        title: "Create-directory",
        path: "directory/create",
        element: e.jsx(OJ, {}),
        nav: !0
    }, {
        title: "Update-directory",
        path: "directory/update",
        element: e.jsx(OJ, {}),
        nav: !0
    }, {
        title: "View-directory",