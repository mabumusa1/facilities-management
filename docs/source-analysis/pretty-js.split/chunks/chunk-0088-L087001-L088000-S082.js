                                    children: _?.map(t => e.jsx(A, {
                                        value: t.id,
                                        control: e.jsx(C, {
                                            sx: {
                                                color: "#B6B6B6"
                                            }
                                        }),
                                        label: e.jsx(hp, {
                                            sx: {
                                                fontSize: "14px",
                                                fontWeight: 400
                                            },
                                            children: i(t.name)
                                        })
                                    }, t.id))
                                }), c !== b && m && e.jsx(hp, {
                                    sx: {
                                        fontWeight: 400,
                                        color: "#FF4242"
                                    },
                                    children: m
                                })]
                            }), c === b && e.jsx(E, {
                                sx: {
                                    mt: "25px"
                                },
                                id: "outlined-multiline-static",
                                label: i("cancelForm.tellUsMore"),
                                multiline: !0,
                                rows: 4,
                                onChange: e => {
                                    h(e.target.value), y(!1), f("")
                                },
                                error: g,
                                helperText: g && m
                            })]
                        })
                    }), e.jsx(L, {}), e.jsxs(ap, {
                        row: !0,
                        gap: "10px",
                        minWidth: "200px",
                        sx: {
                            justifyContent: "flex-end",
                            p: "24px 24px 36px 24px"
                        },
                        children: [e.jsx(wp, {
                            sx: {
                                width: "255px"
                            },
                            onClick: T,
                            variant: "text",
                            fullWidth: !0,
                            size: "large",
                            children: i("common.cancel")
                        }), e.jsx(wp, {
                            isLoading: S,
                            sx: {
                                width: "255px"
                            },
                            variant: "contained",
                            fullWidth: !0,
                            size: "large",
                            type: "submit",
                            children: i("common.submit")
                        })]
                    })]
                })
            })
        })
    },
    X2 = {
        position: "absolute",
        top: "50%",
        left: "50%",
        transform: "translate(-50%, -50%)",
        width: "740px",
        bgcolor: "#FFF",
        borderRadius: "16px"
    },
    e3 = t => {
        const {
            i18n: n
        } = Gn();
        return e.jsx(Fw, {
            dateAdapter: Ow,
            adapterLocale: "en" === n.language ? nn : cg,
            children: e.jsx(JT, {
                sx: {
                    width: "100%"
                },
                ...t
            })
        })
    };

function t3({
    availableHours: t,
    selectedHours: n,
    setSelectedHours: r,
    handleTimeSelect: a
}) {
    const {
        t: i
    } = Gn();
    return e.jsx(cP, {
        sx: n3.container,
        children: t?.length ? e.jsx(cP, {
            component: "ul",
            sx: n3.list,
            children: t.map(t => e.jsx(D, {
                onClick: () => (e => {
                    const t = new Date(`2023-10-19T${e}`).getTime();
                    n.includes(t) ? r([]) : (r([t]), a(t))
                })(t),
                sx: {
                    ...n3.item,
                    border: "1px solid " + (n.includes(new Date(`2023-10-19T${t}`).getTime()) ? "#008EA5" : "#E3E3E3"),
                    color: "" + (n.includes(new Date(`2023-10-19T${t}`).getTime()) ? "#008EA5" : "#232425")
                },
                children: t
            }, t))
        }) : e.jsx(rP, {
            light: !0,
            color: "#B6B6B6",
            s: 14,
            width: "90%",
            textAlign: "center",
            children: i("thereIsNoAvailability")
        })
    })
}
const n3 = {
        container: {
            width: "100%",
            overflowY: "auto",
            maxHeight: "170px",
            "::-webkit-scrollbar": {
                display: "none"
            }
        },
        list: {
            display: "flex",
            justifyContent: "center",
            alignItems: "center",
            flexFlow: "row wrap",
            rowGap: "8px",
            listStyle: "none",
            padding: "0",
            alignSelf: "center"
        },
        item: {
            backgroundColor: "#FFF",
            textAlign: "center",
            display: "flex",
            justifyContent: "center",
            alignItems: "center",
            borderRadius: "8px",
            mr: "8px",
            cursor: "pointer",
            textTransform: "capitalize",
            transition: "0.2s ease all",
            fontWeight: 700,
            fontSize: "12px",
            width: "85px",
            height: "45px"
        }
    },
    r3 = async (e, t) => {
        const n = t ? `&query=${t}` : "";
        return await lo(`/api-management/rf/requests/sub-categories?category_id=${e}${n}`)
    }, a3 = async (e, t) => {
        const n = t ? `?query=${t}` : "";
        return await lo(`/api-management/rf/requests/types/list/${e}${n}`)
    }, i3 = async e => await lo(`/api-management/rf/requests/types/${e}`), o3 = async e => await co(`/api-management/rf/requests/sub-categories/change-status/${e}`), s3 = async e => await co(`/api-management/rf/requests/types/change-status/${e}`), l3 = async e => await po(`/api-management/rf/requests/types/${e}`), d3 = async () => await lo("/api-management/rf/requests/categories"), c3 = async e => await co(`/api-management/rf/requests/categories/change-status/${e}`), u3 = async (e, t) => await lo(`/api-management/rf/users/requests/available-slots?rf_sub_category_id=${e}&start_date=${t}`), p3 = async e => await lo(`/api-management/rf/requests/service-settings/${e}`), h3 = async e => await co("/api-management/rf/requests/service-settings/updateOrCreate", e), m3 = ({
        subCategoryId: t,
        isOpen: n,
        setIsOpen: r,
        date: a,
        setDate: i,
        successFunc: o
    }) => {
        const {
            t: s
        } = Gn(), [l, c] = Dt.useState(() => Fj().startOf("month").format("YYYY-MM-DD")), [u, p] = Dt.useState([]);
        Dt.useEffect(() => p([]), []);
        const {
            data: h,
            isLoading: m
        } = tl([RF, t, l], async () => await u3(t, l), {
            enabled: !!t && n
        }), f = e => {
            const t = Fj(e).startOf("month").format("YYYY-MM-DD");
            c(t)
        }, g = () => {
            i(null), r(!1)
        }, y = h?.data?.blocked_days || [], v = h?.data?.available_hours?.[Fj(a).format("YYYY-MM-DD")] || [];
        return e.jsx(mt, {
            open: n,
            onClose: g,
            "aria-labelledby": "modal-modal-title",
            "aria-describedby": "modal-modal-description",
            children: e.jsx(e.Fragment, {
                children: e.jsxs(ap, {
                    column: !0,
                    fullWidth: !0,
                    sx: f3,
                    children: [e.jsxs(ap, {
                        column: !0,
                        gap: "6px",
                        children: [e.jsx(hp, {
                            s: 24,
                            bold: !0,
                            children: s("scheduleTime")
                        }), e.jsx(hp, {
                            s: 16,
                            gray: !0,
                            light: !0,
                            children: s("pleaseSelectAnAppropriateDate")
                        })]
                    }), m && e.jsx(ap, {
                        center: !0,
                        sx: {
                            my: "24px",
                            position: "relative",
                            top: m ? "190px" : "0px"
                        },
                        children: e.jsx(d, {
                            size: 30
                        })
                    }), e.jsx(e3, {
                        disabled: m,
                        disablePast: !0,
                        onChange: e => i(e),
                        onMonthChange: f,
                        onYearChange: f,
                        shouldDisableDate: e => y?.includes(Fj(e).format("YYYY-MM-DD")),
                        label: s("dashboard.recordTransaction.datePH"),
                        slotProps: {
                            actionBar: {
                                actions: []
                            },
                            toolbar: {
                                toolbarTitle: s("selectDate")
                            }
                        },
                        sx: {
                            width: "100%",
                            visibility: m ? "hidden" : "visible"
                        }
                    }), a && e.jsxs(ap, {
                        column: !0,
                        children: [e.jsx(hp, {
                            bold: !0,
                            s: 18,
                            sx: {
                                ml: "12px"
                            },
                            children: s("availableTimes")
                        }), e.jsx(t3, {
                            availableHours: v,
                            selectedHours: u,
                            setSelectedHours: p,
                            handleTimeSelect: e => i(Fj(a).format("YYYY-MM-DD") + " " + Fj(e).format("HH:mm"))
                        })]
                    }), e.jsxs(ap, {
                        row: !0,
                        gap: "10px",
                        children: [e.jsx(wp, {
                            variant: "text",
                            fullWidth: !0,
                            size: "large",
                            sx: {
                                mt: "24px"
                            },
                            onClick: () => g(),
                            children: s("common.close")
                        }), e.jsx(wp, {
                            disabled: !a || !u?.length,
                            onClick: () => (async () => {
                                a && u?.length ? (await i(Fj(a).format("YYYY-MM-DD") + " " + Fj(u[0]).format("HH:mm")), await o()) : r(!0)
                            })(),
                            variant: "contained",
                            fullWidth: !0,
                            size: "large",
                            sx: {
                                mt: "24px"
                            },
                            children: s("common.save")
                        })]
                    })]
                })
            })
        })
    }, f3 = {
        position: "absolute",
        top: "50%",
        left: "50%",
        transform: "translate(-50%, -50%)",
        width: {
            xs: "90%",
            sm: "441px"
        },
        bgcolor: "#FFF",
        borderRadius: "16px",
        boxShadow: 24,
        py: "16px",
        px: "24px"
    }, g3 = ({
        handleClose: t,
        isOpen: n,
        id: r,
        subCategoryId: a
    }) => {
        const {
            t: i
        } = Gn(), o = Ys();
        Dt.useEffect(() => {
            n && c(null)
        }, []);
        const [s, l] = Dt.useState(!1), [d, c] = Dt.useState(null), {
            mutate: u
        } = nl({
            mutationFn: e => (async e => {
                const {
                    id: t,
                    dateTime: n
                } = e, r = {
                    date_time: n
                };
                return await uo(`/api-management/rf/users/requests/${t}`, r)
            })(e),
            onSuccess: () => {
                o.invalidateQueries({
                    queryKey: [hF]
                }), t(), l(!0)
            }
        }), p = () => {
            l(!1)
        };
        return e.jsxs(e.Fragment, {
            children: [e.jsx(m3, {
                subCategoryId: a,
                isOpen: n,
                setIsOpen: t,
                date: d,
                setDate: c,
                successFunc: () => {
                    t(), (e => {
                        if (!e) return Zi.error(i("pleaseSelectDate"));
                        const t = Fj(e).format("YYYY-MM-DD HH:mm:ss");
                        u({
                            id: r,
                            dateTime: t
                        })
                    })(d)
                }
            }), e.jsx(lh, {
                variant: "success",
                isOpen: s,
                onDialogClose: () => {},
                closeBtnText: i("close"),
                content: {
                    title: i("rescheduleConfirmed") + "!",
                    body: i("requestSuccessfullyRescheduled")
                },
                renderCloseBtn: () => e.jsx(wp, {
                    color: "success",
                    onClick: p,
                    children: i("close")
                })
            })]
        })
    };

function y3(e) {
    return e <= 0 ? "00" : e < 10 ? `0${e}` : e
}

function v3(e, t = "amount") {
    return e?.reduce((e, n) => e + (parseFloat(n[t]) || 0), 0) || 0
}
const _3 = {
        mx: "4px",
        whiteSpace: "nowrap",
        width: {
            lg: "100%",
            xl: "auto"
        }
    },
    x3 = ({
        children: t,
        action: n,
        requestType: r
    }) => {
        const a = (e => {
            switch (e) {
                case fU.homeServices:
                    return $I.HomeServices;
                case fU.neighbourhoodServices:
                    return $I.NeighbourhoodServices;
                default:
                    return ""
            }
        })(r);
        if (!a) return null;
        const i = "cancel" === n ? qI.Cancel : qI.Update;
        return e.jsx(oi, {
            I: i,
            this: a,
            children: t
        })
    },
    b3 = ({
        onOpenCancel: t,
        requestType: n
    }) => {
        const {
            t: r
        } = Gn();
        return e.jsx(x3, {
            action: "cancel",
            requestType: n,
            children: e.jsx(wp, {
                onClick: t,
                sx: _3,
                variant: "outlined",
                color: "error",
                children: r("requests.cancel")
            })
        })
    },
    w3 = ({
        onOpenReschedule: t,
        requestType: n
    }) => {
        const {
            t: r
        } = Gn();
        return e.jsx(x3, {
            action: "reschedule",
            requestType: n,
            children: e.jsx(wp, {
                onClick: t,
                sx: _3,
                variant: "outlined",
                color: "primary",
                fullWidth: !0,
                children: r("reschedule")
            })
        })
    },
    C3 = ({
        onOpenAssign: t,
        requestType: n,
        reassign: r
    }) => {
        const {
            t: a
        } = Gn();
        return e.jsx(x3, {
            action: "assign",
            requestType: n,
            children: e.jsx(wp, {
                onClick: t,
                sx: _3,
                variant: "contained",
                color: "primary",
                fullWidth: !0,
                size: "small",
                children: a(r ? "requests.reAssignStaff" : "requests.assignStaff")
            })
        })
    },
    M3 = ({
        statusId: t,
        requestId: n,
        requestType: r,
        subCategoryId: a
    }) => {
        const [i, o] = Dt.useState(null), s = e => () => o(e), l = () => o(null), d = (({
            onOpenCancel: t,
            onOpenAssign: n,
            onOpenReschedule: r,
            requestType: a
        }) => {
            const i = a === fU.homeServices,
                o = e.jsx(b3, {
                    onOpenCancel: t,
                    requestType: a
                }),
                s = i && e.jsx(w3, {
                    onOpenReschedule: r,
                    requestType: a
                }),
                l = e.jsx(C3, {
                    onOpenAssign: n,
                    requestType: a
                }),
                d = e.jsx(C3, {
                    onOpenAssign: n,
                    requestType: a,
                    reassign: !0
                });
            return {
                [mU.NEW]: e.jsxs(e.Fragment, {
                    children: [o, s, l]
                }),
                [mU.ASSIGN]: e.jsxs(e.Fragment, {
                    children: [s, o, d]
                }),
                [mU.ACCEPT]: e.jsxs(e.Fragment, {
                    children: [s, o]
                }),
                [mU.REJECTED]: e.jsxs(e.Fragment, {
                    children: [o, s, d]
                }),
                [mU.START]: e.jsx(e.Fragment, {
                    children: o
                }),
                [mU.CLOSED]: e.jsx(e.Fragment, {}),
                [mU.CANCEL]: e.jsx(e.Fragment, {}),
                [mU.QUOTE_RAISED]: e.jsx(e.Fragment, {
                    children: o
                }),
                [mU.QUOTE_ACCEPT]: e.jsx(e.Fragment, {
                    children: o
                }),
                [mU.QUOTE_REJECT]: e.jsx(e.Fragment, {
                    children: o
                }),
                [mU.COMPLETE]: e.jsx(e.Fragment, {})
            }
        })({
            onOpenCancel: s("CancellationPopup"),
            onOpenAssign: s("AssignRequestForm"),
            onOpenReschedule: s("ReschedulePopup"),
            requestType: r
        });
        return e.jsxs(e.Fragment, {
            children: [e.jsx(cP, {
                onClick: e => {
                    e.preventDefault(), e.stopPropagation()
                },
                sx: {
                    display: "flex",
                    width: "100%",
                    flexWrap: "nowrap",
                    justifyContent: "flex-end"
                },
                children: t && d[t]
            }), "AssignRequestForm" === i && e.jsx(K2, {
                open: "AssignRequestForm" === i,
                id: n,
                handleClose: l
            }), e.jsx(J2, {
                isOpen: "CancellationPopup" === i,
                id: n,
                handleClose: l
            }), e.jsx(g3, {
                isOpen: "ReschedulePopup" === i,
                id: n,
                handleClose: l,
                subCategoryId: a
            })]
        })
    },
    S3 = ({
        label: t,
        value: n,
        ellipsis: r
    }) => e.jsxs(Pp, {
        alignItems: "center",
        children: [e.jsx(hp, {
            variant: "smallText",
            display: "block",
            children: t
        }), e.jsx(hp, {
            bold: !0,
            sx: {
                display: "flex",
                mt: "8px",
                "line-break": "anywhere",
                ...r && {
                    overflow: "hidden",
                    "text-overflow": "ellipsis",
                    whiteSpace: "nowrap",
                    display: "inline-block",
                    width: "120px"
                }
            },
            children: n || "--"
        })]
    });
var L3, k3 = {};

function T3() {
    if (L3) return k3;
    L3 = 1;
    var e = h();
    Object.defineProperty(k3, "__esModule", {
        value: !0
    }), k3.default = void 0;
    var t = e(jp()),
        n = m();
    return k3.default = (0, t.default)([(0, n.jsx)("path", {
        d: "M11.99 2C6.47 2 2 6.48 2 12s4.47 10 9.99 10C17.52 22 22 17.52 22 12S17.52 2 11.99 2M12 20c-4.42 0-8-3.58-8-8s3.58-8 8-8 8 3.58 8 8-3.58 8-8 8"
    }, "0"), (0, n.jsx)("path", {
        d: "M12.5 7H11v6l5.25 3.15.75-1.23-4.5-2.67z"
    }, "1")], "AccessTime"), k3
}
const j3 = It(T3());
var E3, D3 = {
    exports: {}
};
var V3 = (E3 || (E3 = 1, D3.exports = function() {
    var e, t, n = 1e3,
        r = 6e4,
        a = 36e5,
        i = 864e5,
        o = /\[([^\]]+)]|Y{1,4}|M{1,4}|D{1,2}|d{1,4}|H{1,2}|h{1,2}|a|A|m{1,2}|s{1,2}|Z{1,2}|SSS/g,
        s = 31536e6,
        l = 2628e6,
        d = /^(-|\+)?P(?:([-+]?[0-9,.]*)Y)?(?:([-+]?[0-9,.]*)M)?(?:([-+]?[0-9,.]*)W)?(?:([-+]?[0-9,.]*)D)?(?:T(?:([-+]?[0-9,.]*)H)?(?:([-+]?[0-9,.]*)M)?(?:([-+]?[0-9,.]*)S)?)?$/,
        c = {
            years: s,
            months: l,
            days: i,
            hours: a,
            minutes: r,
            seconds: n,
            milliseconds: 1,
            weeks: 6048e5
        },
        u = function(e) {
            return e instanceof v
        },
        p = function(e, t, n) {
            return new v(e, n, t.$l)
        },
        h = function(e) {
            return t.p(e) + "s"
        },
        m = function(e) {
            return e < 0
        },
        f = function(e) {
            return m(e) ? Math.ceil(e) : Math.floor(e)
        },
        g = function(e) {
            return Math.abs(e)
        },
        y = function(e, t) {
            return e ? m(e) ? {
                negative: !0,
                format: "" + g(e) + t
            } : {
                negative: !1,
                format: "" + e + t
            } : {
                negative: !1,
                format: ""
            }
        },
        v = function() {
            function m(e, t, n) {
                var r = this;
                if (this.$d = {}, this.$l = n, void 0 === e && (this.$ms = 0, this.parseFromMilliseconds()), t) return p(e * c[h(t)], this);
                if ("number" == typeof e) return this.$ms = e, this.parseFromMilliseconds(), this;
                if ("object" == typeof e) return Object.keys(e).forEach(function(t) {
                    r.$d[h(t)] = e[t]
                }), this.calMilliseconds(), this;
                if ("string" == typeof e) {
                    var a = e.match(d);
                    if (a) {
                        var i = a.slice(2).map(function(e) {
                            return null != e ? Number(e) : 0
                        });
                        return this.$d.years = i[0], this.$d.months = i[1], this.$d.weeks = i[2], this.$d.days = i[3], this.$d.hours = i[4], this.$d.minutes = i[5], this.$d.seconds = i[6], this.calMilliseconds(), this
                    }
                }
                return this
            }
            var g = m.prototype;
            return g.calMilliseconds = function() {
                var e = this;
                this.$ms = Object.keys(this.$d).reduce(function(t, n) {
                    return t + (e.$d[n] || 0) * c[n]
                }, 0)
            }, g.parseFromMilliseconds = function() {
                var e = this.$ms;
                this.$d.years = f(e / s), e %= s, this.$d.months = f(e / l), e %= l, this.$d.days = f(e / i), e %= i, this.$d.hours = f(e / a), e %= a, this.$d.minutes = f(e / r), e %= r, this.$d.seconds = f(e / n), e %= n, this.$d.milliseconds = e
            }, g.toISOString = function() {
                var e = y(this.$d.years, "Y"),
                    t = y(this.$d.months, "M"),
                    n = +this.$d.days || 0;
                this.$d.weeks && (n += 7 * this.$d.weeks);
                var r = y(n, "D"),
                    a = y(this.$d.hours, "H"),
                    i = y(this.$d.minutes, "M"),
                    o = this.$d.seconds || 0;
                this.$d.milliseconds && (o += this.$d.milliseconds / 1e3, o = Math.round(1e3 * o) / 1e3);
                var s = y(o, "S"),
                    l = e.negative || t.negative || r.negative || a.negative || i.negative || s.negative,
                    d = a.format || i.format || s.format ? "T" : "",
                    c = (l ? "-" : "") + "P" + e.format + t.format + r.format + d + a.format + i.format + s.format;
                return "P" === c || "-P" === c ? "P0D" : c
            }, g.toJSON = function() {
                return this.toISOString()
            }, g.format = function(e) {
                var n = e || "YYYY-MM-DDTHH:mm:ss",
                    r = {
                        Y: this.$d.years,
                        YY: t.s(this.$d.years, 2, "0"),
                        YYYY: t.s(this.$d.years, 4, "0"),
                        M: this.$d.months,
                        MM: t.s(this.$d.months, 2, "0"),
                        D: this.$d.days,
                        DD: t.s(this.$d.days, 2, "0"),
                        H: this.$d.hours,
                        HH: t.s(this.$d.hours, 2, "0"),
                        m: this.$d.minutes,
                        mm: t.s(this.$d.minutes, 2, "0"),
                        s: this.$d.seconds,
                        ss: t.s(this.$d.seconds, 2, "0"),
                        SSS: t.s(this.$d.milliseconds, 3, "0")
                    };
                return n.replace(o, function(e, t) {
                    return t || String(r[e])
                })
            }, g.as = function(e) {
                return this.$ms / c[h(e)]
            }, g.get = function(e) {
                var t = this.$ms,
                    n = h(e);
                return "milliseconds" === n ? t %= 1e3 : t = "weeks" === n ? f(t / c[n]) : this.$d[n], t || 0
            }, g.add = function(e, t, n) {
                var r;
                return r = t ? e * c[h(t)] : u(e) ? e.$ms : p(e, this).$ms, p(this.$ms + r * (n ? -1 : 1), this)
            }, g.subtract = function(e, t) {
                return this.add(e, t, !0)
            }, g.locale = function(e) {
                var t = this.clone();
                return t.$l = e, t
            }, g.clone = function() {
                return p(this.$ms, this)
            }, g.humanize = function(t) {
                return e().add(this.$ms, "ms").locale(this.$l).fromNow(!t)
            }, g.valueOf = function() {
                return this.asMilliseconds()
            }, g.milliseconds = function() {
                return this.get("milliseconds")
            }, g.asMilliseconds = function() {
                return this.as("milliseconds")
            }, g.seconds = function() {
                return this.get("seconds")
            }, g.asSeconds = function() {
                return this.as("seconds")
            }, g.minutes = function() {
                return this.get("minutes")
            }, g.asMinutes = function() {
                return this.as("minutes")
            }, g.hours = function() {
                return this.get("hours")
            }, g.asHours = function() {
                return this.as("hours")
            }, g.days = function() {
                return this.get("days")
            }, g.asDays = function() {
                return this.as("days")
            }, g.weeks = function() {
                return this.get("weeks")
            }, g.asWeeks = function() {
                return this.as("weeks")
            }, g.months = function() {
                return this.get("months")
            }, g.asMonths = function() {
                return this.as("months")
            }, g.years = function() {
                return this.get("years")
            }, g.asYears = function() {
                return this.as("years")
            }, m
        }(),
        _ = function(e, t, n) {
            return e.add(t.years() * n, "y").add(t.months() * n, "M").add(t.days() * n, "d").add(t.hours() * n, "h").add(t.minutes() * n, "m").add(t.seconds() * n, "s").add(t.milliseconds() * n, "ms")
        };
    return function(n, r, a) {
        e = a, t = a().$utils(), a.duration = function(e, t) {
            var n = a.locale();
            return p(e, {
                $l: n
            }, t)
        }, a.isDuration = u;
        var i = r.prototype.add,
            o = r.prototype.subtract;
        r.prototype.add = function(e, t) {
            return u(e) ? _(this, e, 1) : i.bind(this)(e, t)
        }, r.prototype.subtract = function(e, t) {
            return u(e) ? _(this, e, -1) : o.bind(this)(e, t)
        }
    }
}()), D3.exports);
const A3 = It(V3);

function O3({
    start: t,
    showIcon: n = !0,
    stop: r = !1,
    endDate: a = ""
}) {
    const {
        i18n: {
            language: i
        }
    } = Gn();
    Fj.extend(A3);
    const [{
        days: o,
        hours: s,
        minutes: l,
        seconds: d
    }, c] = Dt.useState({
        days: 0,
        hours: 0,
        minutes: 0,
        seconds: 0
    }), [u, p] = Dt.useState(0), h = Dt.useCallback(() => {
        const e = Fj(a).valueOf() !== Fj(t).valueOf() && r ? Math.abs(Fj(a).valueOf() - Fj(t).valueOf()) : Math.abs(Fj().valueOf() - Fj(t).valueOf());
        p(e);
        const n = Fj.duration(e),
            i = Math.floor(n.asDays()),
            o = n.hours(),
            s = n.minutes(),
            l = n.seconds();
        c({
            days: i,
            hours: o,
            minutes: s,
            seconds: l
        })
    }, [a, t, r]);
    return Dt.useEffect(() => {
        if (Fj(a).valueOf() !== Fj(t).valueOf() && r) return void h();
        const e = setInterval(() => h(), 1e3);
        return () => {
            clearInterval(e)
        }
    }, [r, t, a]), e.jsx(ap, {
        sx: {
            display: "flex",
            alignItems: "center"
        },
        children: !!u && e.jsxs(e.Fragment, {
            children: [n && e.jsx(j3, {
                color: "primary"
            }), e.jsxs(hp, {
                variant: "button",
                children: [o > 0 && e.jsx(hp, {
                    variant: "button",
                    sx: {
                        display: "inline-block",
                        ml: 4
                    },
                    children: `${o} ${CZ(o,i)} - `
                }), " ", " ", y3(s), ":", y3(l), ":", y3(d)]
            })]
        })
    })
}
const P3 = ({
        request: t
    }) => {
        const {
            t: n
        } = Gn();
        return e.jsx(lP, {
            flex: !0,
            row: !0,
            alignItems: "center",
            children: e.jsxs(ap, {
                children: [e.jsx(hp, {
                    variant: "smallText",
                    children: n("Since request start")
                }), e.jsx(hp, {
                    variant: "body",
                    bold: !0,
                    sx: {
                        display: "flex",
                        mt: "8px",
                        "line-break": "anywhere",
                        "& .MuiTypography-root": {
                            color: e => e.palette.primary.main
                        }
                    },
                    children: e.jsx(O3, {
                        showIcon: !1,
                        start: t?.startDate,
                        stop: [mU.CANCEL, mU.CLOSED, mU.REJECTED].includes(String(t?.status?.id))
                    })
                })]
            })
        })
    },
    I3 = ({
        icon: t
    }) => {
        const {
            CurrentBrand: n
        } = Gc();
        return e.jsx(lP, {
            sx: {
                display: "flex",
                justifyContent: "center",
                alignItems: "center"
            },
            children: e.jsx(cP, {
                children: e.jsx(cP, {
                    component: "img",
                    src: t ?? "data:image/png;base64,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",
                    sx: {
                        filter: qc[n]?.primaryPalette?.primary_filter,
                        maxWidth: "42px",
                        maxHeight: "42px"
                    }
                })
            })
        })
    },
    F3 = {
        statusFilterValues: {
            "requests.filter.progress": [{
                name: "status.1",
                value: !0,
                id: "New",
                status: mU.NEW
            }, {
                name: "status.2",
                value: !0,
                id: "Assigned",
                status: mU.ASSIGN
            }, {
                name: "status.3",
                value: !0,
                id: "Completed",
                status: mU.CLOSED
            }, {
                name: "status.4",
                value: !0,
                id: "Cancelled",
                status: mU.CANCEL
            }, {
                name: "status.5",
                value: !0,
                id: "Started",
                status: mU.START
            }, {
                name: "status.6",
                value: !0,
                id: "Accepted",
                status: mU.ACCEPT
            }, {
                name: "status.7",
                value: !0,
                id: "Quote raised",
                status: mU.QUOTE_RAISED
            }, {
                name: "status.8",
                value: !0,
                id: "Quote accepted",
                status: mU.QUOTE_ACCEPT
            }, {
                name: "status.9",
                value: !0,
                id: "Quote rejected",
                status: mU.QUOTE_REJECT
            }, {
                name: "status.10",
                value: !0,
                id: "Rejected",
                status: mU.REJECTED
            }]
        },
        statusColors: {
            [mU.NEW]: _U,
            [mU.ASSIGN]: xU,
            [mU.CLOSED]: bU,
            [mU.CANCEL]: wU,
            [mU.START]: CU,
            [mU.ACCEPT]: bU,
            [mU.QUOTE_RAISED]: CU,
            [mU.QUOTE_ACCEPT]: bU,
            [mU.QUOTE_REJECT]: wU,
            [mU.REJECTED]: wU,
            [mU.COMPLETE]: bU
        },
        statusVariants: {
            [mU.NEW]: "neutral",
            [mU.ACCEPT]: "success",
            [mU.COMPLETE]: "success",
            [mU.QUOTE_ACCEPT]: "success",