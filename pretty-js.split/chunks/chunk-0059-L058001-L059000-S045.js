                        previousData: n
                    }
                }
            });
        Dt.useEffect(() => {
            n && (a(n), t.invalidateQueries([DF]))
        }, [n]);
        const a = e => {
                e.forEach(e => {
                    const {
                        is_active: t,
                        subject: n
                    } = e, r = "facilities" === n, a = "0" === t, s = i(n) && !o(n);
                    a ? (ni.update(ni.rules.filter(e => e.subject !== n || e.subject === n && "VIEW" !== e.action)), r && ni.update(ni.rules.filter(e => "bookings" !== e.subject || "bookings" === e.subject && "VIEW" !== e.action))) : s && (ni.update([...ni.rules, {
                        subject: n,
                        action: "VIEW"
                    }]), r && ni.update([...ni.rules, {
                        subject: "bookings",
                        action: "VIEW"
                    }]))
                })
            },
            i = t => e?.permissions?.find(e => e.subject === t),
            o = e => ni.can("VIEW", e);
        return {
            modules: n,
            isModuleEnabled: e => {
                const t = n?.find(t => t.id === e);
                return "1" === t?.is_active
            },
            toggleModule: e => {
                const t = n?.find(t => t.id === e);
                r(t?.id)
            }
        }
    },
    FU = t => e.jsx(i, {
        ...t,
        inheritViewBox: !0,
        children: e.jsxs("svg", {
            width: "20",
            height: "26",
            viewBox: "0 0 20 26",
            fill: "none",
            xmlns: "http://www.w3.org/2000/svg",
            children: [e.jsx("mask", {
                id: "mask0_45844_16712",
                maskUnits: "userSpaceOnUse",
                x: "0",
                y: "0",
                width: "20",
                height: "26",
                children: e.jsx("path", {
                    d: "M11.1111 1.0812C11.1111 0.484117 11.6086 0 12.2222 0H18.8889C19.5025 0 20 0.484117 20 1.0812V24.8682C20 25.4653 19.5025 25.9493 18.8889 25.9493H1.11109C0.4975 25.9493 0 25.4653 0 24.8681V14.056C0 13.4588 0.4975 12.9748 1.11109 12.9748H5.55562V7.56858C5.55562 6.97142 6.05305 6.4873 6.66672 6.4873H11.1111V1.0812Z",
                    fill: "white"
                })
            }), e.jsx("g", {
                mask: "url(#mask0_45844_16712)",
                children: e.jsx("path", {
                    d: "M20.0002 0V25.9493H11.1113V0H20.0002Z",
                    fill: "#969798"
                })
            }), e.jsx("mask", {
                id: "mask1_45844_16712",
                maskUnits: "userSpaceOnUse",
                x: "0",
                y: "0",
                width: "20",
                height: "26",
                children: e.jsx("path", {
                    d: "M11.1111 1.0812C11.1111 0.484117 11.6086 0 12.2222 0H18.8889C19.5025 0 20 0.484117 20 1.0812V24.8682C20 25.4653 19.5025 25.9493 18.8889 25.9493H1.11109C0.4975 25.9493 0 25.4653 0 24.8681V14.056C0 13.4588 0.4975 12.9748 1.11109 12.9748H5.55562V7.56858C5.55562 6.97142 6.05305 6.4873 6.66672 6.4873H11.1111V1.0812Z",
                    fill: "white"
                })
            }), e.jsx("g", {
                mask: "url(#mask1_45844_16712)",
                children: e.jsx("path", {
                    d: "M14.4437 7.67669V26.0575H5.55469V6.59549H13.3325C13.9461 6.59549 14.4436 7.07953 14.4436 7.67669",
                    fill: "black",
                    "fill-opacity": "0.2"
                })
            }), e.jsx("mask", {
                id: "mask2_45844_16712",
                maskUnits: "userSpaceOnUse",
                x: "0",
                y: "0",
                width: "20",
                height: "26",
                children: e.jsx("path", {
                    d: "M11.1111 1.0812C11.1111 0.484117 11.6086 0 12.2222 0H18.8889C19.5025 0 20 0.484117 20 1.0812V24.8682C20 25.4653 19.5025 25.9493 18.8889 25.9493H1.11109C0.4975 25.9493 0 25.4653 0 24.8681V14.056C0 13.4588 0.4975 12.9748 1.11109 12.9748H5.55562V7.56858C5.55562 6.97142 6.05305 6.4873 6.66672 6.4873H11.1111V1.0812Z",
                    fill: "white"
                })
            }), e.jsx("g", {
                mask: "url(#mask2_45844_16712)",
                children: e.jsx("path", {
                    d: "M14.4437 8.10917V26.4899H5.55469V7.02789H13.3325C13.9461 7.02789 14.4436 7.51201 14.4436 8.10917",
                    fill: "black",
                    "fill-opacity": "0.18"
                })
            }), e.jsx("mask", {
                id: "mask3_45844_16712",
                maskUnits: "userSpaceOnUse",
                x: "0",
                y: "0",
                width: "20",
                height: "26",
                children: e.jsx("path", {
                    d: "M11.1111 1.0812C11.1111 0.484117 11.6086 0 12.2222 0H18.8889C19.5025 0 20 0.484117 20 1.0812V24.8682C20 25.4653 19.5025 25.9493 18.8889 25.9493H1.11109C0.4975 25.9493 0 25.4653 0 24.8681V14.056C0 13.4588 0.4975 12.9748 1.11109 12.9748H5.55562V7.56858C5.55562 6.97142 6.05305 6.4873 6.66672 6.4873H11.1111V1.0812Z",
                    fill: "white"
                })
            }), e.jsx("g", {
                mask: "url(#mask3_45844_16712)",
                children: e.jsx("path", {
                    d: "M14.4437 7.56859V25.9493H5.55469V6.4873H13.3325C13.9461 6.4873 14.4436 6.97142 14.4436 7.56859",
                    fill: "#B6B6B6"
                })
            }), e.jsx("mask", {
                id: "mask4_45844_16712",
                maskUnits: "userSpaceOnUse",
                x: "0",
                y: "0",
                width: "20",
                height: "26",
                children: e.jsx("path", {
                    d: "M11.1111 1.0812C11.1111 0.484117 11.6086 0 12.2222 0H18.8889C19.5025 0 20 0.484117 20 1.0812V24.8682C20 25.4653 19.5025 25.9493 18.8889 25.9493H1.11109C0.4975 25.9493 0 25.4653 0 24.8681V14.056C0 13.4588 0.4975 12.9748 1.11109 12.9748H5.55562V7.56858C5.55562 6.97142 6.05305 6.4873 6.66672 6.4873H11.1111V1.0812Z",
                    fill: "white"
                })
            }), e.jsx("g", {
                mask: "url(#mask4_45844_16712)",
                children: e.jsx("path", {
                    d: "M0 12.9747V25.9493H8.88891V14.056C8.88891 13.4588 8.39141 12.9747 7.77781 12.9747L0 12.9747Z",
                    fill: "#CACACA"
                })
            })]
        })
    }),
    HU = (e, t, n) => {
        const {
            data: r,
            isLoading: a
        } = tl([HF], async () => await LU(), {}), i = Dt.useMemo(() => r?.map(e => e.id) || [], [r]), {
            isModuleEnabled: o,
            modules: s
        } = IU(), l = Dt.useMemo(() => [{
            to: "/dashboard",
            text: "sidebar.dashboard",
            icon: KN.DashboardLine,
            enable: t?.can(qI.View, $I.Dashboard)
        }, {
            to: "/properties-list",
            text: "sidebar.property",
            icon: YN.CommunityLine,
            isLinkActive: e => e.includes("/properties-list"),
            links: [{
                to: "/properties-list/communities",
                text: "properties.communities",
                enable: !0,
                isLinkActive: e => ["/community", "/communities"].some(t => e.includes(t)) || "/properties-list" === e
            }, {
                to: "/properties-list/buildings",
                text: "properties.buildings",
                enable: !0,
                isLinkActive: e => ["/building", "/buildings"].some(t => e.includes(t)) && !e.includes("/communities")
            }, {
                to: "/properties-list/units",
                text: "properties.units",
                enable: !0,
                isLinkActive: e => ["/unit", "/new-unit"].some(t => e.includes(t))
            }],
            enable: t?.can(qI.View, $I.Properties)
        }, {
            to: "/marketplace",
            text: "sidebar.marketplace",
            icon: YN.HomeLine,
            isLinkActive: e => e.includes("/marketplace"),
            links: [{
                to: "/marketplace/customers",
                text: "sidebar.customers",
                enable: t.can(qI.View, $I.Customers),
                isLinkActive: e => ["/customer", "/customers"].some(t => e.includes(t)) || "/marketplace" === e
            }, {
                to: "/marketplace/listing",
                text: "sidebar.listing",
                enable: t.can(qI.View, $I.Listings),
                isLinkActive: e => ["/listing", "/listings"].some(t => e.includes(t)) || "/marketplace" === e
            }],
            enable: !!qc?.[n]?.marketPlaceUrl && t.can(qI.View, $I.MarketPlaces) && (t.can(qI.View, $I.Customers) || t.can(qI.View, $I.Listings))
        }, {
            to: "/",
            text: "sales",
            icon: BN.BarChartBoxLine,
            isLinkActive: e => ["/dashboard/visits", "booking-contracts"].some(t => e.includes(t)) || "/marketplace" === e,
            links: [{
                to: "/dashboard/visits",
                text: "sidebar.visits",
                enable: t.can(qI.View, $I.Visits),
                isLinkActive: e => ["/dashboard/visits"].some(t => e.includes(t)) || "/marketplace" === e
            }, {
                to: "/dashboard/booking-contracts",
                text: "sidebar.bookingContracts",
                isLinkActive: e => ["/dashboard/booking-contracts"].some(t => e.includes(t)) || "/dashboard/booking-contracts" === e,
                enable: !!qc?.[n]?.enableContractsBooking && t?.can(qI.View, $I.BookingAndContracts) && t.can(qI.View, $I.bookingUnits)
            }],
            enable: !!qc?.[n]?.marketPlaceUrl && (t?.can(qI.View, $I.BookingAndContracts) || t.can(qI.View, $I.Visits))
        }, {
            to: "/leasing",
            text: "sidebar.leasing",
            icon: WN.DraftLine,
            enable: t?.can(qI.View, $I.Quotes) || t?.can(qI.View, $I.Applications) || t?.can(qI.View, $I.Leases),
            isLinkActive: e => e.includes("/leasing"),
            links: [{
                to: "/leasing/visits",
                text: "sidebar.visits",
                enable: t.can(qI.View, $I.Visits),
                isLinkActive: e => ["leasing/visits"].some(t => e.includes(t)) || "/marketplace" === e
            }, {
                to: "/leasing/apps",
                text: "sidebar.applications-list",
                enable: t?.can(qI.View, $I.Applications),
                isLinkActive: e => e.includes("/apps")
            }, {
                to: "/leasing/quotes",
                text: "sidebar.quotes",
                enable: t?.can(qI.View, $I.Quotes),
                isLinkActive: e => e.includes("/quotes")
            }, {
                to: "/leasing/leases",
                text: "sidebar.leases-list",
                enable: t?.can(qI.View, $I.Leases),
                isLinkActive: e => e.includes("/leases")
            }]
        }, {
            to: "/requests",
            text: "sidebar.requests",
            icon: UN.HammerLine,
            isLinkActive: e => e.includes("/requests"),
            links: [{
                to: `/requests?type=${fU.homeServices}`,
                text: "requests.unitsService",
                enable: e?.ENABLE_REQUESTS && t.can(qI.View, $I.HomeServices) && i?.includes(fU.homeServices),
                isLinkActive: (e, t) => {
                    const n = new URLSearchParams(t).get("type");
                    return +n === fU.homeServices || e.includes("/requests") && null === n
                }
            }, {
                to: `/requests?type=${fU.neighbourhoodServices}`,
                text: "requests.commonArea",
                enable: e?.ENABLE_REQUESTS && t.can(qI.View, $I.NeighbourhoodServices) && i?.includes(fU.neighbourhoodServices),
                isLinkActive: (e, t) => +new URLSearchParams(t).get("type") === fU.neighbourhoodServices
            }],
            enable: e?.ENABLE_REQUESTS && (t.can(qI.View, $I.HomeServices) || t.can(qI.View, $I.NeighbourhoodServices)) && i?.length > 1
        }, {
            to: "/visitor-access",
            text: "sidebar.visitorAccess",
            icon: WN.ContactsBookLine,
            enable: e?.ENABLE_REQUESTS && t.can(qI.View, $I.VisitorAccess) && o(PU.VISITOR)
        }, {
            icon: BN.Calendar2,
            text: "dashboard.quickAccess.bookings",
            to: "/dashboard/bookings",
            enable: e?.ENABLE_BOOKING_REQUESTS && t?.can(qI.View, $I.Bookings) && o(PU.FACILITIES)
        }, {
            to: "/transactions",
            text: "sidebar.transactions",
            icon: BN.CalculatorLine,
            enable: t?.can(qI.View, $I.Transactions)
        }, {
            icon: zN.Message3Line,
            text: "dashboard.quickAccess.communication",
            to: "/dashboard/offers",
            isLinkActive: e => ["/offers", "/directory", "/suggestions"].some(t => e.includes(t)),
            roles: [pU.Marketing],
            links: [{
                text: "dashboard.quickAccess.offers",
                to: "/dashboard/offers",
                roles: [pU.Marketing],
                isLinkActive: e => e.includes("/offers"),
                enable: e?.ENABLE_OFFERS && t?.can(qI.View, $I.Offers) && o(PU.OFFERS)
            }, {
                text: "dashboard.quickAccess.directory",
                path: "/dashboard/directory",
                to: "/dashboard/directory",
                isLinkActive: e => e.includes("/directory"),
                enable: e?.ENABLE_DIRECTORY && t?.can(qI.View, $I.Directories) && o(PU.DIRECTORY)
            }, {
                text: "dashboard.quickAccess.suggestions",
                to: "/dashboard/suggestions",
                isLinkActive: e => e.includes("/suggestions"),
                roles: [pU.HomeRequests, pU.NeighborhoodRequests, pU.Marketing, pU.Leasing],
                enable: e?.ENABLE_SUGGESTION && t?.can(qI.View, $I.Suggestions)
            }],
            enable: e?.ENABLE_OFFERS && t?.can(qI.View, $I.Offers) && o(PU.OFFERS)
        }, {
            to: "/contacts",
            text: "sidebar.contacts",
            icon: WN.ContactsBook2Line,
            links: [{
                to: `/contacts/${uU.Tenant}`,
                text: "contacts.roles.CUSTOMER",
                enable: e?.ENABLE_TENANTS && t?.can(qI.View, $I.Tenants)
            }, {
                to: `/contacts/${uU.Owner}`,
                text: "contacts.roles.OWNER",
                enable: e?.ENABLE_OWNERS && t?.can(qI.View, $I.Owners)
            }, {
                to: `/contacts/${uU.Manager}`,
                text: "contacts.roles.MANAGEMENT",
                enable: e?.ENABLE_MANGERS && t?.can(qI.View, $I.Managers)
            }, {
                to: `/contacts/${uU.ServiceProfessional}`,
                text: "contacts.roles.MAINTENANCE",
                enable: e?.ENABLE_PROFESSIONALS && t?.can(qI.View, $I.ServiceProfessionals)
            }],
            enable: !0
        }, {
            to: "/dashboard/reports",
            text: "dashboard.quickAccess.reports",
            icon: FU,
            enable: t?.can(qI.View, $I.Reports) && (t?.can(qI.View, $I.SystemReports) || t.can(qI.View, $I.PowerBiReports)),
            links: [{
                to: "/dashboard/system-reports",
                text: "breadcrumb.system-reports",
                enable: t?.can(qI.View, $I.SystemReports)
            }, {
                to: "/dashboard/power-bi-reports",
                text: "Power BI Reports",
                enable: t.can(qI.View, $I.PowerBiReports)
            }]
        }], [e, t, n, i, s]);
        return {
            links: l.filter(e => !1 !== e.enable),
            isLoading: a
        }
    };
var NU, RU = {};

function YU() {
    if (NU) return RU;
    NU = 1;
    var e = h();
    Object.defineProperty(RU, "__esModule", {
        value: !0
    }), RU.default = void 0;
    var t = e(jp()),
        n = m();
    return RU.default = (0, t.default)((0, n.jsx)("path", {
        d: "M6 10c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2m12 0c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2m-6 0c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2"
    }), "MoreHoriz"), RU
}
const BU = It(YU()),
    zU = ["/dashboard", "/properties-list", "/requests", "/transactions", "/more"];
const UU = Vt.memo(function() {
    const {
        t: t
    } = Gn(), n = Ft(), {
        pathname: r
    } = Ht(), {
        planFeatures: a
    } = Qc(), {
        CurrentBrand: i
    } = Gc(), o = ii(), {
        links: s,
        isLoading: l
    } = HU(a, o, i), d = Dt.useMemo(() => zU.map(e => "/more" === e ? {
        to: "/more",
        text: "sidebar.more",
        icon: BU
    } : s.find(t => t.to === e)).filter(Boolean), [s]), c = Dt.useMemo(() => {
        const e = d.findIndex(e => !!e && (r === e.to || ("/more" === e.to && "/more" === r || (!(!("isLinkActive" in e) || "function" != typeof e.isLinkActive || !e.isLinkActive(r)) || !("/dashboard" === e.to || "/more" === e.to || !r.startsWith(e.to))))));
        return e >= 0 ? e : 0
    }, [r, d]), u = Dt.useCallback((e, t) => {
        const r = d[t];
        r?.to && n(r.to)
    }, [d, n]);
    return !d?.length || l ? e.jsx(e.Fragment, {}) : e.jsx(Ye, {
        showLabels: !0,
        sx: {
            position: "fixed",
            bottom: 0,
            left: 0,
            right: 0,
            zIndex: 1100,
            height: 66,
            backgroundColor: "background.paper",
            borderTop: 1,
            borderColor: "divider",
            px: 1,
            "& .MuiBottomNavigationAction-root": {
                color: "text.secondary",
                minWidth: 48,
                padding: "6px 4px"
            },
            "& .MuiBottomNavigationAction-icon": {
                transform: "scale(0.8)"
            },
            "& .MuiBottomNavigationAction-root.Mui-selected": {
                color: "primary.main"
            },
            "& .MuiBottomNavigationAction-label": {
                mt: 3,
                fontSize: "1.4rem"
            }
        },
        value: c,
        onChange: u,
        children: d?.map(n => n ? e.jsx(Be, {
            label: t(n.text),
            icon: n.icon ? e.jsx(n.icon, {}) : e.jsx(e.Fragment, {})
        }, n.to) : e.jsx(e.Fragment, {}))
    })
});
var WU, ZU = {};

function qU() {
    if (WU) return ZU;
    WU = 1;
    var e = h();
    Object.defineProperty(ZU, "__esModule", {
        value: !0
    }), ZU.default = void 0;
    var t = e(jp()),
        n = m();
    return ZU.default = (0, t.default)((0, n.jsx)("path", {
        d: "m12 8-6 6 1.41 1.41L12 10.83l4.59 4.58L18 14z"
    }), "ExpandLess"), ZU
}
const $U = It(qU());
var GU, KU = {};

function QU() {
    if (GU) return KU;
    GU = 1;
    var e = h();
    Object.defineProperty(KU, "__esModule", {
        value: !0
    }), KU.default = void 0;
    var t = e(jp()),
        n = m();
    return KU.default = (0, t.default)((0, n.jsx)("path", {
        d: "M16.59 8.59 12 13.17 7.41 8.59 6 10l6 6 6-6z"
    }), "ExpandMore"), KU
}
const JU = It(QU());
var XU, eW = {};

function tW() {
    if (XU) return eW;
    XU = 1;
    var e = h();
    Object.defineProperty(eW, "__esModule", {
        value: !0
    }), eW.default = void 0;
    var t = e(jp()),
        n = m();
    return eW.default = (0, t.default)((0, n.jsx)("path", {
        d: "M11.99 2C6.47 2 2 6.48 2 12s4.47 10 9.99 10C17.52 22 22 17.52 22 12S17.52 2 11.99 2m6.93 6h-2.95c-.32-1.25-.78-2.45-1.38-3.56 1.84.63 3.37 1.91 4.33 3.56M12 4.04c.83 1.2 1.48 2.53 1.91 3.96h-3.82c.43-1.43 1.08-2.76 1.91-3.96M4.26 14C4.1 13.36 4 12.69 4 12s.1-1.36.26-2h3.38c-.08.66-.14 1.32-.14 2 0 .68.06 1.34.14 2zm.82 2h2.95c.32 1.25.78 2.45 1.38 3.56-1.84-.63-3.37-1.9-4.33-3.56m2.95-8H5.08c.96-1.66 2.49-2.93 4.33-3.56C8.81 5.55 8.35 6.75 8.03 8M12 19.96c-.83-1.2-1.48-2.53-1.91-3.96h3.82c-.43 1.43-1.08 2.76-1.91 3.96M14.34 14H9.66c-.09-.66-.16-1.32-.16-2 0-.68.07-1.35.16-2h4.68c.09.65.16 1.32.16 2 0 .68-.07 1.34-.16 2m.25 5.56c.6-1.11 1.06-2.31 1.38-3.56h2.95c-.96 1.65-2.49 2.93-4.33 3.56M16.36 14c.08-.66.14-1.32.14-2 0-.68-.06-1.34-.14-2h3.38c.16.64.26 1.31.26 2s-.1 1.36-.26 2z"
    }), "Language"), eW
}
const nW = It(tW());
var rW, aW = {};

function iW() {
    if (rW) return aW;
    rW = 1;
    var e = h();
    Object.defineProperty(aW, "__esModule", {
        value: !0
    }), aW.default = void 0;
    var t = e(jp()),
        n = m();
    return aW.default = (0, t.default)([(0, n.jsx)("path", {
        d: "M5 5h6c.55 0 1-.45 1-1s-.45-1-1-1H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h6c.55 0 1-.45 1-1s-.45-1-1-1H5z"
    }, "0"), (0, n.jsx)("path", {
        d: "m20.65 11.65-2.79-2.79c-.32-.32-.86-.1-.86.35V11h-7c-.55 0-1 .45-1 1s.45 1 1 1h7v1.79c0 .45.54.67.85.35l2.79-2.79c.2-.19.2-.51.01-.7"
    }, "1")], "LogoutRounded"), aW
}
const oW = It(iW());

function sW({
    item: t,
    isMobile: n,
    pathname: r,
    search: a,
    selectedTab: i,
    handleClick: s,
    handleNavigate: l,
    getTabColor: d,
    isTabSelected: c,
    checkCollapse: u,
    t: p,
    open: h,
    onClickOverride: m
}) {
    const {
        text: f,
        to: g,
        icon: y,
        links: v = [],
        isLinkActive: _
    } = t, x = !!v?.length;
    return e.jsxs(e.Fragment, {
        children: [e.jsxs(ye, {
            button: !0,
            onClick: () => {
                m ? m() : x ? s(f) : g && l(g, f)
            },
            children: [e.jsx(ze, {
                children: y && e.jsx(y, {
                    sx: n ? {
                        width: "25px",
                        height: "25px",
                        color: e => d({
                            to: g,
                            links: v,
                            text: f,
                            theme: e
                        }) || _?.(r) ? e?.palette?.primary.main : "inherit"
                    } : {
                        color: e => d({
                            to: g,
                            links: v,
                            text: f,
                            theme: e
                        }) || _?.(r) ? e?.palette?.primary.main : "inherit"
                    }
                })
            }), e.jsx(Ue, {
                sx: void 0 !== h ? {
                    display: h ? "block" : "none"
                } : void 0,
                primary: e.jsx(o, {
                    sx: {
                        fontSize: "14px !important",
                        fontWeight: 700,
                        color: e => d({
                            to: g,
                            links: v,
                            text: f,
                            theme: e
                        }) || _?.(r) ? e?.palette?.primary.main : "inherit"
                    },
                    children: p(f)
                })
            }), x && (c(g, v, f) || i === f || _?.(r) ? e.jsx($U, {
                color: c(g, v, f) || _?.(r) ? "primary" : "inherit"
            }) : e.jsx(JU, {}))]
        }), x && e.jsx(We, {
            in: u(f, v) || _?.(r),
            timeout: "auto",
            unmountOnExit: !0,
            children: v?.filter(e => e.enable).map(({
                text: t,
                to: n,
                selectedTab: i,
                isLinkActive: s
            }) => e.jsx(ye, {
                component: "div",
                disablePadding: !0,
                children: e.jsxs(Ze, {
                    onClick: () => {
                        l(n, "", i)
                    },
                    children: [e.jsx(ze, {}), e.jsx(Ue, {
                        primary: e.jsx(o, {
                            sx: {
                                fontSize: "14px !important",
                                fontWeight: 700,
                                color: e => d({
                                    to: n,
                                    links: [],
                                    text: t,
                                    theme: e
                                }) || s?.(r, a) ? e?.palette?.primary.main : "inherit"
                            },
                            children: p(t)
                        })
                    })]
                })
            }, t))
        })]
    })
}

function lW({
    open: t
}) {
    const {
        t: n,
        i18n: r
    } = Gn(), a = s(), i = ce(a.breakpoints.down("sm")), o = Ht(), l = Ft(), d = ii(), {
        pathname: c,
        search: u
    } = Ht(), {
        show: p
    } = nc(), [h, m] = Dt.useState(""), [, f] = Dt.useState(0);
    Dt.useEffect(() => {
        const e = no.on("force-sidebar-refresh", () => {
            f(e => e + 1)
        });
        return () => {
            e()
        }
    }, []);
    const {
        CurrentBrand: g
    } = Gc(), {
        planFeatures: y,
        logOut: v
    } = Qc(), {
        currentLanguage: _,
        setCurrentLanguage: x
    } = nu(), b = Ys(), {
        links: w
    } = HU(y, d, g), C = e => m(h === e ? "" : e), M = (e, t = "", n = 0) => {
        m(t), l(e, {
            state: {
                selectedTab: n
            }
        })
    }, S = ({
        to: e,
        links: t,
        text: n,
        theme: r
    }) => {
        if (o?.pathname === e || o?.pathname + window.location.search === e) return r?.palette?.primary?.main;
        if (t?.length) {
            if (k(t)) return r?.palette?.primary?.main
        } else if (h === n) return r?.palette?.primary?.main;
        return ""
    }, k = e => e?.some(e => o?.pathname + window.location.search === e?.to), T = (e, t, n) => {
        if (o?.pathname === e || o?.pathname + window.location.search === e) return !0;
        if (t?.length) {
            if (k(t)) return !0
        } else if (h === n) return !0;
        return !1
    }, j = (e, t) => h === e || !!k(t), E = [{
        key: "support",
        text: "sidebar.support",
        icon: BH,
        onClickOverride: p,
        visible: !0
    }, {
        key: "changeLanguage",
        text: "sidebar.changeLanguage",
        icon: nW,
        onClickOverride: () => {
            const e = Jc.find(e => e.code !== _?.code);
            if (!e) return;
            const t = e.code;
            r.changeLanguage(t), b.clear(), document.documentElement.lang = t, bo.defaults.headers.common["X-App-Locale"] = t, bo.defaults.headers["X-App-Locale"] = t, x(e)
        },
        visible: i
    }, {
        key: "logout",
        text: "drawer.logout",
        icon: oW,
        onClickOverride: v,
        visible: i
    }];
    return e.jsxs(e.Fragment, {
        children: [w?.map(t => e.jsx(sW, {
            t: n,
            item: t,
            isMobile: i,
            pathname: c,
            search: u,
            selectedTab: h,
            handleClick: C,
            handleNavigate: M,
            getTabColor: S,
            isTabSelected: T,
            checkCollapse: j
        }, t.text)), e.jsx(L, {
            variant: "middle"
        }), E?.filter(e => e.visible).map(r => e.jsx(sW, {
            item: r,
            isMobile: i,
            pathname: c,
            search: u,
            selectedTab: h,
            handleClick: C,
            handleNavigate: M,
            getTabColor: S,
            isTabSelected: T,
            checkCollapse: j,
            t: n,
            open: t,
            onClickOverride: r.onClickOverride
        }, r.key))]
    })
}
const dW = G(ge)(({
        theme: e
    }) => ({
        "& .MuiListItemButton-root": {
            borderRadius: "8px",
            margin: "5px 0",
            color: e.palette.primary.text1,
            "&:hover": {
                backgroundColor: e.palette.action.hover
            },
            "&.Mui-selected ": {
                color: e?.palette?.primary?.main,
                backgroundColor: "rgba(0, 0, 0, 0.00)"
            },
            "& .MuiListItemIcon-root": {
                color: "inherit"
            },
            "&$disabled": {
                opacity: .5
            }
        }
    })),
    cW = ({
        handleDrawerToggle: t,
        open: n
    }) => {
        const r = Ft(),
            {
                CurrentBrand: a
            } = Gc();
        return e.jsxs(e.Fragment, {
            children: [e.jsx(qe, {
                component: "img",
                sx: {
                    px: [12],
                    marginTop: 5,
                    marginBottom: {
                        xs: 0,
                        sm: 4
                    },
                    width: "auto",
                    maxWidth: {
                        xs: "150px",
                        sm: "250px"
                    },
                    height: "auto",
                    maxHeight: {
                        xs: "50px",
                        sm: "130px"
                    },
                    objectFit: "contain",
                    cursor: "pointer"
                },
                alt: "The house from the offer.",
                src: qc?.[a]?.logo,
                onClick: () => r("/dashboard")
            }), e.jsx(dW, {
                sx: {
                    py: 8
                },
                children: e.jsx(lW, {
                    toggleDrawer: t,
                    open: n
                })
            })]
        })
    };

function uW() {
    const t = Ft(),
        n = t => e.jsx(i, {
            ...t,
            inheritViewBox: !0,
            children: e.jsx("path", {
                d: "M20 17.5H22V19.5H2V17.5H4V10.5C4 8.37827 4.84285 6.34344 6.34315 4.84315C7.84344 3.34285 9.87827 2.5 12 2.5C14.1217 2.5 16.1566 3.34285 17.6569 4.84315C19.1571 6.34344 20 8.37827 20 10.5V17.5ZM18 17.5V10.5C18 8.9087 17.3679 7.38258 16.2426 6.25736C15.1174 5.13214 13.5913 4.5 12 4.5C10.4087 4.5 8.88258 5.13214 7.75736 6.25736C6.63214 7.38258 6 8.9087 6 10.5V17.5H18ZM9 21.5H15V23.5H9V21.5Z"
            })
        }),
        {
            data: r
        } = tl([LF], async () => await (async () => {
            const e = await lo("/api-management/notifications/unread-count");
            return e?.data
        })(), {
            refetchOnMount: "always"
        });
    return e.jsx($e, {
        badgeContent: r?.count,
        color: "error",
        style: {
            cursor: "pointer"
        },
        onClick: () => t("/notifications"),
        anchorOrigin: {
            vertical: "top",
            horizontal: "right"
        },
        children: e.jsx(n, {
            sx: {
                color: "#7F7F7F",
                fontSize: 24
            }
        })
    })
}

function pW({
    handleClose: t,
    isOpen: n
}) {
    const {
        t: r
    } = Gn(), {
        user: i
    } = Qc();
    return e.jsx(v, {
        onClose: t,
        open: n,
        fullWidth: !0,
        maxWidth: "lg",
        children: e.jsxs(lP, {
            sx: {
                position: "relative",
                overflowX: "hidden",
                "&::after": {
                    content: '""',
                    position: "absolute",
                    backgroundImage: `url(${Mc})`,
                    backgroundRepeat: "no-repeat",
                    backgroundPosition: "center center",
                    backgroundSize: "cover",
                    width: 300,
                    height: 300,
                    bottom: "0",
                    left: "0",
                    transform: "scaleX(-1) rotate(90deg)"
                }
            },
            children: [e.jsx(a, {
                sx: {
                    height: 50,
                    width: 50,
                    position: "absolute",
                    top: 20,
                    right: 20,
                    zIndex: 30,
                    borderRadius: "50%",
                    display: "flex",
                    justifyContent: "center",
                    alignItems: "center",
                    cursor: "pointer",
                    fontSize: 20,
                    border: "1px solid rgba(0,0,0,0.1)",
                    transition: "0.1s linear",
                    "&:hover": {
                        backgroundColor: "rgba(0,0,0,0.1)"
                    }
                },
                onClick: t,
                children: "X"
            }), e.jsx(b, {
                sx: {
                    textAlign: "center",
                    my: 10,
                    position: "relative"
                },
                children: r("popup.My Digital ID")
            }), e.jsx(_, {
                sx: {
                    mb: "30px"
                },
                children: e.jsxs(lP, {
                    sx: {
                        display: {
                            xs: "grid",
                            md: "flex"
                        },
                        justifyContent: "center",
                        alignItems: "center",
                        gap: 15
                    },
                    children: [e.jsxs(lP, {
                        sx: {
                            display: "flex",
                            justifyContent: "center",
                            alignItems: "start",
                            flexDirection: "column",
                            zIndex: 10
                        },
                        children: [e.jsx(Ie, {
                            component: "img",
                            sx: {
                                width: 200,
                                height: 200,
                                border: "2px solid white"
                            },
                            image: i?.image,
                            alt: "picture"
                        }), e.jsx(o, {
                            variant: "subtitle1",
                            sx: {
                                fontWeight: 500,
                                textTransform: "capitalize"
                            },
                            children: i?.name
                        }), e.jsxs(o, {
                            variant: "subtitle2",
                            sx: {
                                fontWeight: 400
                            },
                            children: [r("popup.Member Since"), " ", i?.membership_date]
                        })]
                    }), e.jsx(Ie, {
                        component: "img",
                        sx: {
                            width: 250,
                            height: 250
                        },
                        image: `data:image/svg+xml;base64, ${i?.qr_code}`,
                        alt: "picture"
                    })]
                })
            })]
        })
    })
}
var hW, mW = {};

function fW() {
    if (hW) return mW;
    hW = 1;
    var e = h();
    Object.defineProperty(mW, "__esModule", {
        value: !0
    }), mW.default = void 0;
    var t = e(jp()),
        n = m();
    return mW.default = (0, t.default)((0, n.jsx)("path", {
        d: "M6.23 20.23 8 22l10-10L8 2 6.23 3.77 14.46 12z"
    }), "ArrowForwardIos"), mW
}
const gW = It(fW());
var yW, vW = {};

function _W() {
    if (yW) return vW;
    yW = 1;
    var e = h();
    Object.defineProperty(vW, "__esModule", {
        value: !0
    }), vW.default = void 0;
    var t = e(jp()),
        n = m();
    return vW.default = (0, t.default)((0, n.jsx)("path", {
        d: "M11.67 3.87 9.9 2.1 0 12l9.9 9.9 1.77-1.77L3.54 12z"
    }), "ArrowBackIos"), vW
}
const xW = It(_W());
var bW, wW = {};

function CW() {
    if (bW) return wW;
    bW = 1;
    var e = h();
    Object.defineProperty(wW, "__esModule", {
        value: !0
    }), wW.default = void 0;
    var t = e(jp()),
        n = m();
    return wW.default = (0, t.default)((0, n.jsx)("path", {
        d: "m16.81 8.94-3.75-3.75L4 14.25V18h3.75zM6 16v-.92l7.06-7.06.92.92L6.92 16zm13.71-9.96c.39-.39.39-1.02 0-1.41l-2.34-2.34c-.2-.2-.45-.29-.71-.29-.25 0-.51.1-.7.29l-1.83 1.83 3.75 3.75zM2 20h20v4H2z"
    }), "BorderColorOutlined"), wW
}
const MW = It(CW());
var SW, LW = {};

function kW() {
    if (SW) return LW;
    SW = 1;
    var e = h();
    Object.defineProperty(LW, "__esModule", {
        value: !0
    }), LW.default = void 0;
    var t = e(jp()),
        n = m();
    return LW.default = (0, t.default)((0, n.jsx)("path", {
        d: "M11 7h2v2h-2zm0 4h2v6h-2zm1-9C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2m0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8"
    }), "InfoOutlined"), LW
}
const TW = It(kW());
var jW, EW = {};

function DW() {
    if (jW) return EW;
    jW = 1;
    var e = h();
    Object.defineProperty(EW, "__esModule", {
        value: !0
    }), EW.default = void 0;
    var t = e(jp()),
        n = m();
    return EW.default = (0, t.default)((0, n.jsx)("path", {
        d: "m17 8-1.41 1.41L17.17 11H9v2h8.17l-1.58 1.58L17 16l4-4zM5 5h7V3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h7v-2H5z"
    }), "LogoutOutlined"), EW
}
const VW = It(DW());
var AW, OW = {};

function PW() {
    if (AW) return OW;