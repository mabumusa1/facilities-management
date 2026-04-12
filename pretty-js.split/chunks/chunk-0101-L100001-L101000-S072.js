            queryFn: async () => await u3(t, m),
            enabled: !!t && n
        }); + c && (s = "00:00", l = "23:00"), Dt.useEffect(() => y([]), [a]);
        const x = () => r(!1),
            b = v?.data?.blocked_days || [];
        return e.jsx(mt, {
            open: n,
            onClose: x,
            "aria-labelledby": "modal-modal-title",
            "aria-describedby": "modal-modal-description",
            children: e.jsx(e.Fragment, {
                children: e.jsxs(cP, {
                    column: !0,
                    fullWidth: !0,
                    sx: V9.style,
                    children: [e.jsxs(cP, {
                        column: !0,
                        gap: "6px",
                        children: [e.jsx(rP, {
                            s: 24,
                            bold: !0,
                            children: h("scheduleTime")
                        }), e.jsx(rP, {
                            s: 16,
                            gray: !0,
                            light: !0,
                            children: h("pleaseSelectAnAppropriateDate")
                        })]
                    }), (u || _ && !!t) && e.jsx(cP, {
                        center: !0,
                        sx: {
                            my: "24px",
                            position: "relative",
                            top: u || _ ? "190px" : "0px"
                        },
                        children: e.jsx(d, {
                            size: 30
                        })
                    }), e.jsx(e3, {
                        disabled: u,
                        disablePast: !0,
                        onChange: e => i(Fj(e)),
                        onMonthChange: e => {
                            const t = Fj(e).startOf("month").format("YYYY-MM-DD");
                            f(t)
                        },
                        shouldDisableDate: e => b?.length ? b?.includes(Fj(e).format("YYYY-MM-DD")) : !p?.includes(e?.getDay()),
                        label: "dashboard.recordTransaction.datePH",
                        slotProps: {
                            actionBar: {
                                actions: []
                            }
                        },
                        sx: {
                            width: "100%",
                            visibility: u ? "hidden" : "visible"
                        }
                    }), a && e.jsxs(cP, {
                        column: !0,
                        children: [e.jsx(rP, {
                            bold: !0,
                            s: 18,
                            sx: {
                                ml: "12px"
                            },
                            children: h("availableTimes")
                        }), !u && e.jsx(D9, {
                            startTime: s,
                            endTime: l,
                            interval: 60,
                            selectedHours: g,
                            setSelectedHours: y,
                            date: a
                        })]
                    }), e.jsxs(cP, {
                        row: !0,
                        gap: "10px",
                        children: [e.jsx(dP, {
                            variant: "text",
                            fullWidth: !0,
                            size: "large",
                            sx: {
                                mt: "24px"
                            },
                            onClick: x,
                            children: h("close")
                        }), e.jsx(dP, {
                            disabled: !a || !g?.length,
                            onClick: () => {
                                if (a && g?.length) {
                                    const e = Fj(a).format("YYYY-MM-DD"),
                                        t = Fj(g?.[0]).format("HH:mm:ss");
                                    o(e + " " + t)
                                } else r(!0)
                            },
                            variant: "contained",
                            fullWidth: !0,
                            size: "large",
                            sx: {
                                mt: "24px"
                            },
                            children: h("save")
                        })]
                    })]
                })
            })
        })
    },
    D9 = ({
        startTime: t = "08:00",
        endTime: n = "15:00",
        interval: r = 30,
        selectedHours: a,
        setSelectedHours: i,
        date: o
    }) => {
        const [s, l] = Dt.useState([]), {
            t: d
        } = Gn();
        Dt.useEffect(() => {
            const e = [];
            let a = new Date(`2023-10-19T${t}`);
            const i = new Date(`2023-10-19T${n}`);
            if (!!o && Fj(o).isSame(Fj(), "day")) {
                const e = new Date;
                let t = e.getHours(),
                    n = e.getMinutes();
                n > 0 && (t += 1, n = 0), t > 23 && (t = 23, n = 59), a.setHours(t, n, 0, 0)
            }
            for (; a <= i;) e.push(a.toTimeString().slice(0, 5)), a = new Date(a.getTime() + 60 * r * 1e3);
            l(e)
        }, [t, n, r, o]);
        return e.jsx(cP, {
            sx: V9.container,
            children: s.length ? e.jsx(cP, {
                component: "ul",
                sx: V9.list,
                children: s.map(t => e.jsx(D, {
                    onClick: () => (e => {
                        const t = new Date(`2023-10-19T${e}`).getTime();
                        a.includes(t) ? i([]) : i([t])
                    })(t),
                    sx: {
                        ...V9.item,
                        border: e => a.includes(new Date(`2023-10-19T${t}`).getTime()) ? `1px solid ${e.palette.primary.main}` : `1px solid ${e.palette.grey[200]}`,
                        color: e => a.includes(new Date(`2023-10-19T${t}`).getTime()) ? e.palette.primary.main : e.palette.text.primary
                    },
                    children: t
                }, t))
            }) : e.jsx(rP, {
                light: !0,
                color: "#B6B6B6",
                s: 14,
                width: "90%",
                textAlign: "center",
                children: d("thereIsNoAvailability")
            })
        })
    },
    V9 = {
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
            marginRight: "8px",
            cursor: "pointer",
            textTransform: "capitalize",
            transition: "0.2s ease all",
            fontWeight: 700,
            fontSize: "12px",
            width: "85px",
            height: "45px"
        },
        style: {
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
        }
    };

function A9({
    label: t = "",
    allText: n,
    openSelector: r,
    chosenItems: a,
    dataKey: i = "name",
    fullWidth: o = !1,
    disabled: s = !1,
    sx: l = {}
}) {
    const d = Dt.useMemo(() => a?.length ? a?.map(e => e?.[i]).join(", ") : null, [a, i]);
    return e.jsxs(cP, {
        column: !0,
        gap: "8px",
        children: [e.jsx(rP, {
            s: 14,
            light: !0,
            sx: {
                color: s ? "#CACACA" : "#232425"
            },
            children: t
        }), e.jsxs(cP, {
            xbetween: !0,
            sx: {
                border: "1px solid #E5E5E5",
                borderRadius: 2,
                padding: "14px 16px",
                cursor: s ? "default" : "pointer",
                width: o ? "100%" : 400,
                backgroundColor: s ? "#F5F5F5" : "white",
                "&:hover": {
                    backgroundColor: s ? "F5F5F5" : "#F5F5F5"
                },
                ...l
            },
            onClick: () => {
                s || r()
            },
            children: [e.jsx(rP, {
                s: 16,
                light: !0,
                sx: {
                    overflow: "hidden",
                    textOverflow: "ellipsis",
                    whiteSpace: "nowrap"
                },
                children: d || n
            }), e.jsx(Bf, {
                color: "inherit",
                sx: {
                    width: 20,
                    height: 20
                }
            })]
        })]
    })
}
const O9 = ["Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday", "Sunday"];

function P9() {
    const {
        t: e
    } = Gn(), [t, n] = Dt.useState(!1), [r, a] = Dt.useState(!1), i = bf({
        resolver: L1(v1().shape({
            type: a1().required(),
            start_time: WX().nullable().when("type", {
                is: "custom",
                then: e => e.required("marketSettings.startTimeIsRequired")
            }),
            end_time: WX().nullable().when("type", {
                is: "custom",
                then: e => e.required("marketSettings.endTimeIsRequired")
            }),
            days: x1().test("has-truthy", e("marketSettings.pleaseSelectAtLeastOneWorkingDay"), e => e && e.some(e => !!e))
        })),
        defaultValues: {
            type: "all-day",
            start_time: null,
            end_time: null,
            days: O9
        },
        mode: "onChange"
    }), {
        data: o,
        isLoading: s,
        refetch: l
    } = tl({
        queryKey: ["VISITS_SETTINGS"],
        queryFn: $J,
        onSuccess: e => {
            i.setValue("type", "1" === e?.data?.is_all_day ? "all-day" : "custom"), i.setValue("start_time", e?.data?.start_time), i.setValue("end_time", e?.data?.end_time), i.setValue("days", O9.map(t => !!e?.data?.days?.includes(t) && t))
        },
        cacheTime: 0
    });
    return {
        data: o,
        isLoading: s,
        form: i,
        onSubmit: async t => {
            const r = "all-day" === t?.type,
                i = {
                    days: t?.days?.filter(e => e),
                    start_time: t?.start_time,
                    end_time: t?.end_time,
                    is_all_day: r ? 1 : 0
                };
            r && (delete i.start_time, delete i.end_time), a(!0), n(!1);
            try {
                await (async e => await co("/api-management/marketplace/admin/settings/visits/store", e))(i), Zi.success(e("marketSettings.visitSettingsUpdated"))
            } catch (o) {} finally {
                a(!1), l()
            }
        },
        isEditEnabled: t,
        setIsEditEnabled: n,
        isSubmitting: r
    }
}
const I9 = e => {
    if (!e) return e;
    const t = String(e);
    return t.startsWith("+") ? `‪${t}‬` : t
};
const F9 = async e => {
    try {
        const n = (e => {
                const {
                    search: t,
                    filter: n,
                    page: r,
                    sort: a
                } = e, i = {
                    query: t || "",
                    page: r || 1,
                    is_paginate: 1
                };
                if (a?.sortBy && (i.sortBy = a.sortBy, i.sortDirection = a.sortDirection || "desc"), n) {
                    const e = Array.isArray(n.status) ? n.status : [],
                        t = Array.isArray(n.source) ? n.source : [],
                        r = Array.isArray(n.assignee) ? n.assignee : [],
                        a = Array.isArray(n.priority) ? n.priority : [],
                        o = Array.isArray(n.type) ? n.type : [];
                    e.length && e.forEach((e, t) => {
                        const n = "object" == typeof e ? e.id : e;
                        i[`statuses[${t}]`] = n
                    }), t.length && t.forEach((e, t) => {
                        const n = "object" == typeof e ? e.id : e;
                        i[`sources[${t}]`] = n
                    });
                    const [s] = r;
                    s && (i.assignee_id = s.id);
                    const [l] = a;
                    l && (i.priority = "object" == typeof l ? l.id : l);
                    const [d] = o;
                    d && (i.role = "object" == typeof d ? d.id : d)
                }
                return i
            })(e),
            r = await lo("/api-management/rf/leads", n);
        return t = r, {
            list: t?.data?.list?.map(e => {
                let t = null;
                "buy" === e.interested ? t = "Buying" : "rent" === e.interested ? t = "Renting" : e.interested && (t = e.interested);
                const n = e.lead_owner ? {
                    name: e.lead_owner.name,
                    phoneNumber: e.lead_owner.phone_number
                } : void 0;
                return {
                    id: e.id,
                    name: e.name,
                    phoneNumber: e.phone_number,
                    email: e.email || "",
                    date: e.created_at,
                    lastModified: e.updated_at,
                    lastContact: e.lead_last_contact_at,
                    interested: t,
                    role: e.role,
                    status: e.status?.id ?? void 0,
                    source: e.source?.id ?? void 0,
                    priority: e.priority?.value ?? void 0,
                    leadOwner: n
                }
            }),
            page: t?.data?.paginator?.current_page,
            total: t?.data?.paginator?.total,
            pagesCount: t?.data?.paginator?.last_page
        }
    } catch (n) {
        throw n
    }
    var t
}, H9 = async ({
    search: e,
    filter: t,
    page: n,
    active_application_lease: r = null
}) => {
    try {
        const o = await lo("/api-management/rf/leads", {
            query: e,
            page: n,
            role: (i = t, i && "object" == typeof i && Object.keys(i).length > 0 ? uZ(Object.keys(t)[0]) : ""),
            is_paginate: 1,
            active_application_lease: r
        });
        return a = o, a?.data?.list?.map(e => {
            let t = null;
            return "buy" === e.interested ? t = "Buying" : "rent" === e.interested ? t = "Renting" : e.interested && (t = e.interested), {
                id: e.id,
                name: e.name,
                phoneNumber: e.phone_number,
                email: e.email || "",
                date: e.created_at,
                interested: t,
                role: e.role
            }
        }) ?? []
    } catch (o) {
        throw o
    }
    var a, i
}, N9 = async (e, t = 0) => {
    try {
        return await lo(`/api-management/rf/leads/${e}`, {
            is_lite: t
        })
    } catch (n) {}
}, R9 = async e => {
    try {
        const n = (t = e, {
            first_name: t?.firstName,
            last_name: t?.lastName,
            national_id: t?.nationalId || null,
            nationality: t?.nationality || null,
            phone_number: t?.phoneNumber,
            phone_country_code: t?.phoneCountryCode?.id,
            email: t?.email || null,
            interested: t?.interested,
            status: t?.status ? "object" == typeof t.status ? parseInt(t.status.id) : parseInt(t.status) : null,
            lead_owner_id: t?.leadOwner?.id ? "string" == typeof t.leadOwner.id ? parseInt(t.leadOwner.id) : t.leadOwner.id : null,
            source: t?.source ? "object" == typeof t.source ? parseInt(t.source.id) : parseInt(t.source) : null,
            priority: t?.priority ? "object" == typeof t.priority ? parseInt(t.priority.id) : parseInt(t.priority) : null
        });
        await co("/api-management/rf/leads", n)
    } catch (n) {
        throw n
    }
    var t
}, Y9 = async e => {
    try {
        const t = await lo(`/api-management/rf/leads/${e}`);
        return (e => {
            const t = {
                    interested: e.interested,
                    status: e.status?.id || null,
                    leadOwner: e.lead_owner ? {
                        id: e.lead_owner?.id,
                        name: e.lead_owner?.name,
                        phoneNumber: I9(e.lead_owner?.phone_number)
                    } : null,
                    source: e.source?.id || null,
                    priority: e.priority?.id || null,
                    lastContact: e.lead_last_contact_at
                },
                n = e.favorites?.map(e => ({
                    id: e.id,
                    communityName: e?.property?.rf_community?.name || "---",
                    unitName: e?.property?.name || "---"
                })) || [],
                r = e.visits?.map(e => ({
                    visitNumber: e.id.toString(),
                    dateTime: e.date_time,
                    communityName: e.community?.name,
                    buildingName: e.building?.name || null,
                    unitName: e.unit?.name,
                    visitOwner: {
                        name: e.user?.name,
                        phoneNumber: I9(e.user?.phone_number)
                    },
                    visitType: 1 === e.type ? "Sales" : "Rental",
                    visitStatus: e.status?.name
                })) || [],
                a = e.bookings?.map(e => {
                    let t = "Ready Sales";
                    return "1" === e.is_buy && (t = "1" === e.is_off_plan_sale ? "Off-Plan Sales" : "Ready Sales"), {
                        bookingNumber: e.id.toString(),
                        communityName: e.unit?.community?.name,
                        buildingName: e.unit?.building?.name || null,
                        unitName: e.unit?.name,
                        bookingOwner: {
                            name: e.user?.name,
                            phoneNumber: I9(e.user?.phone_number)
                        },
                        bookingType: t,
                        bookingStatus: e.last_status?.name
                    }
                }) || [];
            let i = "Lead";
            return "Owners" === e.role ? i = "Owner" : "Tenants" === e.role && (i = "Tenant"), {
                id: e.id,
                fullName: e.name,
                phoneNumber: I9(e.full_phone_number),
                email: e.email,
                customerType: i,
                lastModified: e.updated_at,
                lastContact: e.lead_last_contact_at,
                generalInformation: t,
                interestedProperties: n,
                visits: r,
                bookings: a,
                canDelete: e.can_delete,
                canConvert: "Lead" === i
            }
        })(t.data)
    } catch (t) {
        throw t
    }
}, B9 = async ({
    page: e,
    search: t,
    listed: n,
    active: r,
    communityId: a,
    category_id: i,
    status: o,
    buildingId: s,
    withMissingData: l
}) => {
    try {
        const [d, c] = await Promise.all([await lo("/api-management/marketplace/admin/units", {
            rf_community_id: a,
            is_paginate: 1,
            page: e,
            query: t,
            is_missing_data: null == l ? void 0 : l,
            status_id: o,
            rf_building_id: s,
            category_id: i,
            is_market_place: n ? 1 : 0,
            active: r ? 1 : void 0,
            limit: 50
        }), await lo(`/api-management/marketplace/admin/units/missing/${a}`, {
            rf_community_id: a,
            is_paginate: 1,
            page: e,
            query: t,
            is_missing_data: null == l ? void 0 : l,
            status_id: o,
            rf_building_id: s,
            category_id: i,
            is_market_place: n ? 1 : 0
        })]);
        return (({
            dto: e,
            listed: t,
            statsDto: n
        }) => ({
            list: e?.data?.list?.map(e => ({
                id: e.id,
                name: e.name,
                building: e.building?.name,
                type: e.type?.name,
                subtype: e.category?.name,
                area: e.unit_size,
                price: e.marketplace?.amount_before_tax,
                annualRentPrice: e.marketplace?.amount_before_tax,
                deposit: e.marketplace?.deposit,
                status: {
                    id: e.status?.id,
                    name: e?.status?.name
                },
                isHidden: "0" === e?.is_market_place_prices_visible
            })),
            metadata: {
                total: e?.data?.paginator?.total,
                last_page: e?.data?.paginator?.last_page
            },
            noUnitsMissingData: t ? void 0 : n?.data?.missing_units_count,
            noUnitsUnMissingData: t ? void 0 : n?.data?.un_missing_units_count
        }))({
            dto: d,
            statsDto: c,
            listed: n
        })
    } catch (d) {
        throw d
    }
}, z9 = async ({
    communityId: e,
    status: t,
    buildingId: n,
    unitId: r,
    isListing: a,
    search: i,
    category_id: o
}) => {
    const s = !r;
    try {
        await co(`/api-management/marketplace/admin/units/change-status/${s?`all/${e}`:r}`, {
            status_id: t,
            rf_building_id: n,
            is_market_place: a ? 0 : 1,
            query: i,
            category_id: o
        })
    } catch (l) {
        throw l
    }
}, U9 = ({
    open: t = !1,
    handleClose: n = () => {},
    isSales: r = !1
}) => {
    const [a, i] = Dt.useState(!1), [o, s] = Dt.useState(!1), [l, d] = Dt.useState(!1), [c, u] = Dt.useState(!1), [p, h] = Dt.useState(null), [m, f] = Dt.useState(null), [g, y] = Dt.useState(null), [x, b] = Dt.useState(null), {
        t: w
    } = Gn(), C = () => {
        y(null), f(null), b(null), h(null), k.reset(), n()
    }, {
        data: S,
        isLoading: L
    } = P9(), {
        form: k,
        submitHandler: T,
        isCreating: j
    } = m9({
        visitId: null,
        handleClose: C,
        isSales: r
    });
    Dt.useEffect(() => {
        b(null)
    }, [g]);
    const E = j || !g || !m || !x;
    return e.jsxs(e.Fragment, {
        children: [e.jsx(v, {
            open: t,
            onClose: n,
            maxWidth: "lg",
            children: e.jsxs(_, {
                children: [e.jsx(cP, {
                    component: "header",
                    sx: {
                        display: "flex",
                        justifyContent: "space-between",
                        alignItems: "center",
                        marginBottom: "10px"
                    },
                    children: e.jsx(rP, {
                        sx: {
                            margin: "8px 0"
                        },
                        s: 26,
                        children: w("scheduleVisit")
                    })
                }), e.jsxs(sP, {
                    column: !0,
                    sx: {
                        width: "550px"
                    },
                    children: [e.jsx(lP, {
                        mb: "20px",
                        children: e.jsx(A9, {
                            label: w("selectCustomer") + " *",
                            allText: w("selectCustomer"),
                            openSelector: () => i(!0),
                            fullWidth: !0,
                            chosenItems: [p]
                        })
                    }), e.jsx(lP, {
                        mb: "20px",
                        children: e.jsx(A9, {
                            label: w("selectCommunity") + " *",
                            allText: w("leaseForm.allCommunities"),
                            openSelector: () => s(!0),
                            fullWidth: !0,
                            chosenItems: [g]
                        })
                    }), e.jsx(lP, {
                        mb: "20px",
                        children: e.jsx(A9, {
                            label: w("selectUnit") + " *",
                            allText: w("selectUnit"),
                            openSelector: () => d(!0),
                            fullWidth: !0,
                            chosenItems: [x],
                            disabled: !g?.id
                        })
                    }), e.jsx(lP, {
                        mb: "20px",
                        children: e.jsx(A9, {
                            label: w("selectDateAndTime") + " *",
                            allText: w("selectDateAndTime"),
                            openSelector: () => u(!0),
                            fullWidth: !0,
                            chosenItems: [{
                                name: m || ""
                            }]
                        })
                    })]
                }), e.jsxs(M, {
                    sx: {
                        mt: 0,
                        p: 0
                    },
                    children: [e.jsx(dP, {
                        onClick: C,
                        disabled: j,
                        size: "large",
                        sx: {
                            margin: "15px 0px",
                            width: "100%"
                        },
                        variant: "text",
                        color: "error",
                        type: "submit",
                        children: w("cancel")
                    }), e.jsx(dP, {
                        isLoading: j,
                        onClick: e => {
                            const t = m?.split(" ")[1],
                                n = m?.split(" ")[0];
                            k?.setValue("unitId", x?.id), k?.setValue("day", n), k?.setValue("time", t || ""), k?.setValue("userId", p?.id || null), T()
                        },
                        disabled: E,
                        size: "large",
                        sx: {
                            margin: "15px 0px",
                            width: "100%"
                        },
                        variant: "contained",
                        type: "submit",
                        children: w("popup.save")
                    })]
                })]
            })
        }), e.jsx(Ch, {
            isOpen: a,
            onSave: () => i(!1),
            onClose: () => i(!1),
            fetcher: async ({
                search: e,
                page: t
            }) => {
                const n = await F9({
                    search: e,
                    page: t,
                    filter: {}
                });
                return n?.list
            },
            refetchKey: p?.id,
            setProperties: e => {
                h(e?.[0] || null)
            },
            title: w("selectCustomer"),
            renderSubtitle: t => e.jsx(rP, {
                s: 12,
                light: !0,
                dir: "ltr",
                children: t?.phoneNumber
            }),
            isMultiSelect: !1,
            chosenProperties: p ? [p] : []
        }), e.jsx(Ch, {
            isOpen: o,
            onClose: () => s(!1),
            onSave: () => s(!1),
            fetcher: async ({
                search: e,
                page: t
            }) => {
                const n = await L9({
                    query: e,
                    page: t,
                    active: 1,
                    is_paginate: 1,
                    is_buy: +!!r
                });
                return n?.list
            },
            refetchKey: g?.id,
            setProperties: e => {
                y(e?.[0] || null)
            },
            title: w("selectCommunity"),
            renderSubtitle: t => e.jsx(rP, {
                s: 12,
                light: !0,
                dir: "ltr",
                children: `${t?.city}${t?.city&&","} ${t?.district}`
            }),
            isMultiSelect: !1,
            chosenProperties: g ? [g] : []
        }), l && e.jsx(Ch, {
            isOpen: l,
            onClose: () => d(!1),
            fetcher: async ({
                search: e,
                page: t
            }) => {
                const n = await B9({
                    search: e,
                    page: t,
                    communityId: g?.id,
                    listed: !0,
                    active: !0
                });
                return n?.list
            },
            onSave: () => d(!1),
            refetchKey: g?.id,
            setProperties: e => {
                b(e?.[0] || null)
            },
            title: w("selectUnit"),
            renderSubtitle: t => e.jsx(rP, {
                s: 12,
                light: !0,
                children: t?.building
            }),
            isMultiSelect: !1,
            chosenProperties: x ? [x] : []
        }), e.jsx(E9, {
            isOpen: c,
            setIsOpen: u,
            date: m,
            setDate: f,
            startTime: S?.data?.start_time,
            endTime: S?.data?.end_time,
            isAllDay: S?.data?.is_all_day,
            isLoading: L,
            workingDays: u9(S?.data?.days),
            successFunc: e => {
                u(!1), f(e)
            }
        })]
    })
};

function W9({
    item: t
}) {
    return e.jsxs(ap, {
        column: !0,
        children: [e.jsx(hp, {
            variant: "label",
            bold: !0,
            children: t?.name
        }), e.jsx(hp, {
            variant: "smallText",
            dir: "ltr",
            textAlign: "left",
            children: t?.phoneNumber
        })]
    })
}
const Z9 = async e => {
    try {
        const t = await lo("/api-management/rf/admins", {
            roles: [pU.Leasing, uU.Admin, uU.AccountAdmin, pU.Marketing],
            query: e?.search,
            sort_dir: "latest",
            active: 1,
            is_paginate: e?.is_paginate ?? 1,
            page: e?.page
        });
        return O$(t)
    } catch (t) {
        throw t
    }
}, q9 = ({
    title: t = "visitOwner",
    handleAssign: n,
    selectedVisitID: r = null,
    handleClose: a = () => {}
}) => {
    const {
        t: i
    } = Gn(), [o, s] = Dt.useState(null), [l, d] = Dt.useState(!1);
    return e.jsx(e.Fragment, {
        children: e.jsx(Ch, {
            isOpen: !!r,
            onClose: a,
            fetcher: Z9,
            CustomRowItem: ({
                property: t
            }) => e.jsx(W9, {
                item: {
                    name: t?.name,
                    phoneNumber: t?.phone_number
                }
            }),
            setProperties: e => {
                s(e[0])
            },
            onClear: a,
            onSave: async e => {
                const t = e[0]?.id;
                try {
                    d(!0), await n({
                        itemID: r,
                        userId: t
                    }), s(null), d(!1), Zi.success(i("common.success")), a()
                } catch (o) {
                    d(!1)
                }
            },
            title: i(t),
            isMultiSelect: !1,
            chosenProperties: o ? [o] : [],
            refetchKey: [YH],
            saveBtnText: i("assign"),
            clearBtnText: i("cancel"),
            clearBtnStyles: {
                color: "error.main"
            },
            saveBtnLoading: l,
            isPaginated: !0,
            saveBtnVariant: "contained",
            appendToProperties: [{
                id: null,
                name: i("noassignee")
            }]
        })
    })
};

function $9({
    isSales: t
}) {
    const [n, r] = Dt.useState(!1), [a, i] = Dt.useState(null), {
        t: o
    } = Gn(), s = () => {
        r(!1)
    }, {
        data: l,
        search: d,
        filter: c,
        page: u,
        isLoading: p,
        setSearch: h,
        setFilter: m,
        setPage: f,
        chosenCommunity: g,
        setChosenCommunity: y,
        handleAssignVisitOwner: v
    } = m9({
        handleClose: s,
        isSales: t
    });
    return e.jsxs(e.Fragment, {
        children: [e.jsx(rP, {
            s: 16,
            sx: {
                mb: 6,
                mx: 6
            },
            children: l?.total ? `${o("totalVisits")} : ${l?.total}` : ""
        }), e.jsxs(Ne, {
            sx: {
                mt: "20px",
                "& .MuiContainer-root": {
                    padding: 0
                }
            },
            children: [e.jsx(j9, {
                visits: l,
                search: d,
                filterValues: h9(o),
                page: u,
                isLoading: p,
                setSearch: h,
                setFilter: m,
                setPage: f,
                pagesCount: l?.pagesCount,
                selectedFilters: c,
                chosenCommunity: g,
                setChosenCommunity: y,
                setIsCreateFormOpen: r,
                setAssignVisitOwnerOpen: i
            }), e.jsx(U9, {
                open: n,
                handleClose: s,
                isSales: t
            })]
        }), e.jsx(q9, {
            title: "visitOwner",
            handleAssign: v,
            selectedVisitID: a,