            [mU.CLOSED]: "danger",
            [mU.CANCEL]: "danger",
            [mU.QUOTE_REJECT]: "danger",
            [mU.REJECTED]: "danger",
            [mU.START]: "warning",
            [mU.QUOTE_RAISED]: "warning",
            [mU.ASSIGN]: "primary"
        },
        quotationStatus: {
            [mU.QUOTE_RAISED]: {
                variant: "warning",
                text: "pending"
            },
            [mU.QUOTE_ACCEPT]: {
                variant: "success",
                text: "Approved"
            },
            [mU.COMPLETE]: {
                variant: "success",
                text: "Approved"
            },
            [mU.QUOTE_REJECT]: {
                variant: "danger",
                text: "rejected"
            },
            [mU.CANCEL]: {
                variant: "danger",
                text: "rejected"
            },
            [mU.NEW]: null,
            [mU.ASSIGN]: null,
            [mU.START]: null,
            [mU.CLOSED]: null,
            [mU.ACCEPT]: null,
            [mU.REJECTED]: null
        },
        stepsNames: {
            [mU.NEW]: "status.1",
            [mU.ASSIGN]: "status.2",
            [mU.ACCEPT]: "status.6",
            [mU.CANCEL]: "status.4",
            [mU.COMPLETE]: "status.3",
            [mU.QUOTE_ACCEPT]: "status.8",
            [mU.QUOTE_RAISED]: "status.7",
            [mU.QUOTE_REJECT]: "status.9",
            [mU.START]: "status.5",
            [mU.REJECTED]: "",
            [mU.CLOSED]: ""
        }
    };
const H3 = ({
        request: t
    }) => {
        const {
            fields: n,
            requestType: r,
            isCommonArea: a
        } = function(t) {
            const n = t?.category,
                r = t?.status?.id,
                a = n === fU.neighbourhoodServices,
                {
                    t: i
                } = Gn(),
                o = Dt.useMemo(() => [mU.NEW, mU.ASSIGN, mU.ACCEPT, mU.REJECTED, mU.CANCEL, mU.COMPLETE].includes(String(r)) && !a, [r, n]),
                s = Dt.useMemo(() => [mU.START, mU.QUOTE_RAISED, mU.QUOTE_ACCEPT, mU.QUOTE_REJECT].includes(String(r)), [r, n]);
            return {
                fields: Dt.useMemo(() => e.jsxs(e.Fragment, {
                    children: [e.jsx(ap, {
                        xstart: !0,
                        sx: {
                            display: {
                                xs: "none",
                                sm: "block"
                            }
                        },
                        children: e.jsx(I3, {
                            icon: t?.icon
                        })
                    }), e.jsx(ap, {
                        sx: {
                            display: {
                                xs: "none",
                                sm: "block"
                            }
                        },
                        children: e.jsx(S3, {
                            label: t?.subCategory?.name,
                            value: t?.type
                        })
                    }), e.jsx(ap, {
                        sx: {
                            display: {
                                xs: "block",
                                sm: "none"
                            }
                        },
                        children: e.jsx(S3, {
                            label: i("accounting.category"),
                            value: t?.subCategory?.name
                        })
                    }), e.jsx(ap, {
                        sx: {
                            display: {
                                xs: "block",
                                sm: "none"
                            }
                        },
                        children: e.jsx(S3, {
                            label: i("accounting.subcategory"),
                            value: t?.type
                        })
                    }), e.jsx(S3, {
                        label: i("requests.Ticket ID"),
                        value: String(t?.id)
                    }), !a && e.jsx(S3, {
                        label: i("leasing.unit"),
                        value: t?.unit,
                        ellipsis: !0
                    }), !a && e.jsx(S3, {
                        label: i("Building Name"),
                        value: t?.building ?? i("N/A")
                    }), e.jsx(S3, {
                        label: i("leasing.community"),
                        value: t?.community
                    }), a ? null : s ? e.jsx(P3, {
                        request: t
                    }) : e.jsx(S3, {
                        label: i("requests.scheduleTime"),
                        value: t?.scheduleTime ? Fj(t?.scheduleTime).format("DD/MM/YYYY - HH:MM") : null
                    }), e.jsx(S3, {
                        label: i("headers.status"),
                        value: e.jsx(ih, {
                            variant: F3.statusColors[t?.status?.id],
                            title: t?.status?.name
                        })
                    })]
                }), [n, t, o, s, i]),
                shouldShowCounter: s,
                requestType: n,
                isCommonArea: a
            }
        }(t);
        return e.jsx(et, {
            sx: {
                position: "relative",
                borderRadius: "8px",
                backgroundColor: "#fff",
                transition: "0.3s all ease",
                py: "24px !important",
                "&:hover": {
                    backgroundColor: "#eee",
                    cursor: "pointer"
                }
            },
            "data-testid": "request-card",
            children: e.jsx(cP, {
                component: Wt,
                to: `/requests/${t?.id}?type=${t?.category}`,
                sx: {
                    textDecoration: "none",
                    color: "inherit"
                },
                children: e.jsxs(cP, {
                    sx: {
                        display: "grid",
                        gridTemplateColumns: {
                            xl: a ? "1fr repeat(4, 3fr) 2fr" : "1fr 1.5fr 1.5fr 2fr 2fr 2fr 2fr 2fr 2fr",
                            md: "1fr 1fr",
                            xs: "1fr 1fr"
                        },
                        rowGap: "16px",
                        columnGap: "16px"
                    },
                    children: [n, e.jsx(lP, {
                        onClick: e => e.stopPropagation(),
                        sx: {
                            position: "static",
                            gridColumn: {
                                xs: "1 / -1",
                                lg: "auto"
                            },
                            mt: {
                                xs: 2,
                                md: "10px"
                            },
                            textAlign: {
                                xs: "right",
                                md: "end"
                            },
                            width: {
                                xs: "100%",
                                lg: "50px",
                                xl: "300px"
                            },
                            overflow: {
                                xs: "visible",
                                md: "hidden"
                            }
                        },
                        children: e.jsx(M3, {
                            requestType: r,
                            statusId: t?.status?.id,
                            requestId: t?.id,
                            subCategoryId: t?.subCategory?.id
                        })
                    })]
                })
            })
        })
    },
    N3 = ({
        label: t = "",
        allText: n,
        openSelector: r,
        chosenItems: a,
        dataKey: i = "name",
        fullWidth: o = !1,
        disabled: s = !1,
        sx: l = {},
        LeftIcon: d,
        RightIcon: c,
        onRightIconClick: u,
        showChosenItems: p = !0
    }) => {
        const h = Dt.useMemo(() => a?.length ? p && a?.map(e => e?.[i]).join(", ") : null, [a, i]);
        return e.jsxs(ap, {
            column: !0,
            gap: "8px",
            children: [t && e.jsx(hp, {
                s: 14,
                light: !0,
                sx: {
                    color: s ? "#CACACA" : "#232425"
                },
                children: t
            }), e.jsxs(ap, {
                xbetween: !0,
                ycenter: !0,
                sx: {
                    border: "1px solid #E5E5E5",
                    borderRadius: 2,
                    gap: "4px",
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
                children: [d && e.jsx(d, {
                    sx: {
                        height: 20
                    }
                }), e.jsx(hp, {
                    s: 16,
                    light: !0,
                    sx: {
                        overflow: "hidden",
                        textOverflow: "ellipsis",
                        whiteSpace: "nowrap"
                    },
                    children: h || n
                }), c && a?.length ? e.jsxs(ap, {
                    row: !0,
                    ycenter: !0,
                    gap: "2px",
                    children: [(!p || a?.length > 1) && e.jsx(ap, {
                        center: !0,
                        sx: {
                            mx: "1px",
                            width: "18px",
                            height: "18px",
                            backgroundColor: "primary.main",
                            borderRadius: "50%",
                            fontSize: "12px",
                            fontWeight: 500,
                            color: "#ffffff"
                        },
                        children: a?.length
                    }), e.jsx(w, {
                        sx: {
                            padding: "0px 4px",
                            width: 20,
                            height: 20
                        },
                        onClick: e => {
                            e.stopPropagation(), u?.()
                        },
                        children: e.jsx(c, {
                            sx: {
                                height: 20,
                                cursor: "pointer"
                            }
                        })
                    })]
                }) : e.jsx(Bf, {
                    color: "inherit",
                    sx: {
                        width: 20,
                        height: 20
                    }
                })]
            })]
        })
    },
    R3 = ({
        name: t,
        sx: n = {}
    }) => e.jsx(ap, {
        row: !0,
        alignItems: "center",
        spacing: 12,
        sx: n,
        children: e.jsx(hp, {
            variant: "body",
            children: t
        })
    }),
    Y3 = ({
        disabled: t = !1,
        isOpen: n,
        setIsOpen: r,
        title: a,
        searchPlaceholder: i,
        noDataTitle: o,
        noDataDescription: s,
        selectLabel: l,
        placeholder: d,
        formFieldName: c,
        fetcher: u,
        isPaginated: p = !0,
        refetchKey: h,
        setSelectedValue: m,
        isMultiSelect: f = !1,
        CustomRowItem: g,
        CustomNoData: y,
        LeftIcon: v,
        RightIcon: _,
        sx: x = {},
        onRightIconClick: b,
        rightRadioInput: w = !1,
        top: C = null,
        itemContainerStyles: M = {},
        showChosenItems: S = !0,
        selectedFilters: L,
        value: k,
        staleTime: T,
        allowRetry: j = !1,
        hideSearchBar: E = !1
    }) => {
        const {
            i18n: D
        } = Gn();
        D.language;
        return e.jsxs(e.Fragment, {
            children: [e.jsx(ap, {
                fullWidth: !0,
                children: e.jsx(N3, {
                    label: l ? `${l}*` : "",
                    allText: d,
                    openSelector: () => r(!0),
                    chosenItems: k ?? L?.[c],
                    formFieldName: c,
                    showChosenItems: S,
                    disabled: t,
                    sx: {
                        borderRadius: "8px",
                        width: "100%",
                        ...x
                    },
                    LeftIcon: v,
                    RightIcon: _,
                    onRightIconClick: b
                })
            }), e.jsx(Ch, {
                title: a,
                searchPlaceholder: i,
                isOpen: n,
                onClose: () => r(!1),
                onSave: () => r(!1),
                CustomRowItem: g || R3,
                CustomNoData: y || (() => e.jsx(xp, {
                    title: o,
                    body: s,
                    hideIcon: !0
                })),
                fetcher: u,
                isPaginated: p,
                refetchKey: h,
                chosenProperties: k ?? L?.[c],
                setProperties: e => m(e),
                isMultiSelect: f,
                rightRadioInput: w,
                top: C,
                itemContainerStyles: M,
                staleTime: T,
                allowRetry: j,
                hideSearchBar: E
            })]
        })
    },
    B3 = ({
        filters: t,
        clearForm: n,
        isFilterApplied: r,
        selectedFilters: a,
        search: i,
        setSearch: o
    }) => {
        const {
            t: s
        } = Gn(), l = t?.find(e => e?.formFieldName === yU.SORT);
        return e.jsxs(ap, {
            sx: {
                display: "flex",
                flexWrap: "wrap",
                gap: "10px",
                alignItems: "center",
                width: "fit-content",
                minWidth: "100%",
                overflowX: "auto"
            },
            children: [e.jsx(RQ, {
                search: i,
                handleSearch: o,
                placeholder: s("requestsFilters.searchPlaceholder"),
                isGrayIcon: !0,
                iconSx: {
                    width: 20,
                    height: 20
                },
                containerStyle: {
                    height: "48px",
                    width: {
                        xs: "100%",
                        sm: "350px"
                    },
                    flexBasis: {
                        xs: "100%",
                        sm: "auto"
                    },
                    flexGrow: {
                        xs: 1,
                        sm: 0
                    },
                    minWidth: {
                        xs: "100%",
                        sm: "250px"
                    }
                },
                sx: {
                    width: "100%",
                    mr: 0,
                    "& .MuiInputBase-root": {
                        background: "transparent !important",
                        fontSize: "14px",
                        border: "0px !important"
                    }
                },
                height: "34px"
            }), l && e.jsx(ap, {
                sx: {
                    flex: "0 1 auto",
                    minWidth: "fit-content"
                },
                children: e.jsx(Y3, {
                    ...l,
                    value: [{
                        ...l?.value?.[0],
                        name: s(l?.value?.[0]?.name)
                    }],
                    setSelectedValue: e => {
                        l.setSelectedValue([{
                            ...e?.[0],
                            name: e?.[0]?.translationKey ?? e?.[0]?.name
                        }])
                    }
                })
            }), t.filter(e => e?.formFieldName !== yU.SORT).map(t => e.jsx(ap, {
                sx: {
                    flex: "0 1 auto",
                    minWidth: "fit-content"
                },
                children: e.jsx(Y3, {
                    selectedFilters: a,
                    ...t
                })
            }, t.formFieldName)), r && e.jsxs(wp, {
                variant: "text",
                onClick: n,
                sx: {
                    flex: "0 0 auto",
                    whiteSpace: "nowrap"
                },
                children: [e.jsx(KN.CloseFill, {
                    sx: {
                        width: 16,
                        height: 16,
                        mx: "4px"
                    }
                }), s("requestsFilters.clearAll")]
            })]
        })
    },
    z3 = ({
        requestsList: t,
        isEmpty: n,
        isLoading: r,
        count: a,
        filters: i,
        clearForm: o,
        isFilterApplied: s,
        search: l,
        setSearch: d,
        page: c,
        setPage: u,
        selectedFilters: p
    }) => e.jsxs(e.Fragment, {
        children: [e.jsx(ap, {
            pt: "8px",
            pb: "24px",
            sx: {
                width: "100%",
                overflowX: "auto"
            },
            children: e.jsx(B3, {
                selectedFilters: p,
                filters: i,
                clearForm: o,
                isFilterApplied: s,
                search: l,
                setSearch: d
            })
        }), r && e.jsx(Z2, {}), n && e.jsx(q2, {}), t?.map(t => e.jsx(ap, {
            width: "100%",
            mb: "12px",
            children: e.jsx(H3, {
                request: t
            })
        })), e.jsx(HQ, {
            page: c,
            count: a,
            handler: u
        })]
    }),
    U3 = ({
        path: t,
        sx: n = {},
        iconOnly: r = !1
    }) => {
        const {
            t: a
        } = Gn();
        return e.jsxs(wp, {
            component: Wt,
            to: t,
            color: "inherit",
            sx: {
                border: "1px solid #CACACA",
                ...n
            },
            variant: "outlined",
            title: r ? a("breadcrumb.History") : void 0,
            children: [e.jsx(KN.HistoryLine, {
                sx: {
                    marginRight: r ? 0 : "4px",
                    width: "20px",
                    height: "20px"
                }
            }), !r && a("breadcrumb.History")]
        })
    },
    W3 = "create-service-request-schedule",
    Z3 = "create-service-request-schedule-final-step",
    q3 = ({
        total: t,
        type: n = fU.homeServices,
        isHistory: r,
        showHeader: a = !0
    }) => {
        const {
            t: i
        } = Gn(), o = s(), l = ce(o.breakpoints.down("lg"));
        return a ? e.jsxs(sP, {
            alignItems: "center",
            justifyContent: "space-between",
            children: [e.jsx(lP, {
                xs: 6,
                children: e.jsxs(ap, {
                    column: !0,
                    gap: "4px",
                    sx: {
                        mt: "8px",
                        mb: "32px"
                    },
                    children: [e.jsx(hp, {
                        variant: "h4",
                        fontWeight: 700,
                        children: n === fU.homeServices ? i("unitServices") : i("commonAreaServices")
                    }), !!t && e.jsx(hp, {
                        bold: !0,
                        s: 18,
                        fontWeight: 700,
                        children: `${i("totalRequests")}: ${t}`
                    })]
                })
            }), !r && e.jsxs(lP, {
                xs: 6,
                justifyContent: {
                    xs: "flex-end"
                },
                display: "flex",
                alignItems: "center",
                gap: "8px",
                children: [e.jsx(U3, {
                    path: `/requests/history?type=${n}`,
                    iconOnly: l,
                    sx: l ? {
                        minWidth: "auto",
                        padding: "8px"
                    } : {}
                }), e.jsx($3, {
                    requestType: n,
                    children: e.jsx(wp, {
                        component: Wt,
                        to: `/requests/create?type=${n}`,
                        onClick: () => {
                            ro.event(W3)
                        },
                        sx: l ? {
                            minWidth: "auto",
                            padding: "8px"
                        } : {},
                        variant: "contained",
                        color: "primary",
                        title: i("requests.newRequest"),
                        children: l ? e.jsx(jf, {}) : e.jsxs(e.Fragment, {
                            children: [e.jsx(jf, {
                                sx: {
                                    marginRight: "10px"
                                }
                            }), " ", i("requests.newRequest")]
                        })
                    })
                })]
            })]
        }) : e.jsx(e.Fragment, {
            children: !!t && e.jsx(hp, {
                mb: "12px",
                children: `${i("totalRequests")}: ${t}`
            })
        })
    },
    $3 = ({
        children: t,
        requestType: n
    }) => {
        const r = "object" == typeof n,
            a = {
                [fU.homeServices]: e.jsx(oi, {
                    I: qI.Create,
                    this: $I.HomeServices,
                    children: t
                }),
                [fU.neighbourhoodServices]: e.jsx(oi, {
                    I: qI.Create,
                    this: $I.NeighbourhoodServices,
                    children: t
                })
            };
        return r && n ? a[n] : t
    },
    G3 = async ({
        search: e,
        page: t,
        sort: n
    }) => {
        try {
            const a = await lo("/api-management/rf/communities?is_paginate=1", {
                page: t,
                sortBy: n?.sortBy || "created_at",
                sortDirection: n?.value || "desc",
                search: e
            });
            return r = a, {
                list: r?.data?.list?.map(e => ({
                    id: e.id,
                    name: e.name,
                    city: e.city?.name,
                    district: e.district?.name,
                    noBuildings: e.buildings_count ?? 0,
                    noUnits: e.units_count ?? 0,
                    maps: e.map,
                    logo: e.images?.[0]?.url,
                    is_off_plan_sale: e.is_off_plan_sale
                })) ?? [],
                total: r?.data?.paginator?.total ?? 0,
                pageCount: r?.data?.paginator?.last_page ?? 0
            }
        } catch (a) {
            throw a
        }
        var r
    }, K3 = async ({
        search: e,
        page: t,
        subCategoryId: n
    }) => {
        try {
            const a = await lo("/api-management/rf/communities?is_paginate=1", {
                page: t,
                search: e,
                subcategory_id: n
            });
            return r = a, r?.data?.list?.map(e => ({
                id: e.id,
                name: e.name,
                city: e.city?.name,
                district: e.district?.name,
                noBuildings: e.buildings_count ?? 0,
                noUnits: e.units_count ?? 0,
                logo: e.images?.[0]?.url,
                maps: e.map,
                total: r?.data?.paginator?.total,
                is_off_plan_sale: e.is_off_plan_sale
            })) ?? []
        } catch (a) {
            throw a
        }
        var r
    }, Q3 = async e => {
        try {
            const t = await lo(`/api-management/rf/communities/${e}`);
            return await (async e => ({
                name: e?.data?.name,
                id: e?.data?.id,
                city: e?.data?.city?.name,
                district: e?.data?.district?.name,
                noBuildings: e?.data?.buildings_count ?? 0,
                noUnits: e?.data?.units_count ?? 0,
                images: e?.data?.images,
                noDocuments: e?.data?.documents?.length ?? 0,
                documents: e?.data?.documents,
                currency: `${e?.data?.currency?.name} (${e?.data?.currency?.code})`,
                country: e?.data?.country?.name,
                amenities: e?.data?.amenities,
                description: e?.data?.description,
                location: {
                    latitude: e?.data?.map?.latitude ?? 24.763655142884446,
                    longitude: e?.data?.map?.longitude ?? 46.63817920730441,
                    formattedAddress: e?.data?.map?.formattedAddress,
                    mapsLink: e?.data?.map?.mapsLink,
                    districtName: e?.data?.map?.districtName
                },
                is_market_place: e?.data?.is_market_place
            }))(t)
        } catch (t) {
            throw t
        }
    }, J3 = async e => bo.get("/properties/" + e).then(e => e?.data?.data), X3 = ({
        property: t
    }) => e.jsx(R3, {
        name: t.name || t.title,
        sx: {
            mx: "8px"
        }
    }), e4 = ({
        categoryId: e,
        isHistory: t
    }) => {
        const {
            t: n
        } = Gn(), [r, a] = Dt.useState(!1), [i, o] = Dt.useState(!1), [s, l] = Dt.useState(!1), [d, c] = Dt.useState(!1), [u, p] = Dt.useState(!1), {
            state: {
                filter: h,
                page: m,
                search: f,
                sort: g
            },
            setSearch: y,
            setPage: v,
            setSort: _,
            setFilter: x
        } = AQ({
            defaultFilter: {
                [yU.CATEGORY]: e,
                [yU.STATUS]: [],
                [yU.COMMUNITY]: [],
                [yU.SUB_CATEGORY]: [],
                [yU.TYPE]: [],
                [yU.IS_HISTORY]: t
            },
            defaultSort: {
                sortBy: "created_at",
                sortDirection: "desc",
                name: "creationDateNewToOld",
                id: "desc.created_at"
            }
        }), [b, w] = Dt.useState(g?.sortBy ? [g] : []), C = [{
            formFieldName: yU.SORT,
            value: b,
            setSelectedValue: e => {
                w(e), e?.[0]?.sort_by && _({
                    id: e?.[0]?.id,
                    sortBy: e?.[0]?.sort_by,
                    name: e?.[0]?.name,
                    sortDirection: e?.[0]?.value || e?.[0]?.sortDirection
                })
            },
            CustomRowItem: X3,
            LeftIcon: ZN.LineHeightIcon,
            RightIcon: "desc.created_at" !== g?.id ? KN.CloseFill : void 0,
            onRightIconClick: () => {
                _({
                    sortBy: "",
                    sortDirection: "asc",
                    name: ""
                }), w([])
            },
            isOpen: r,
            setIsOpen: a,
            fetcher: () => (async e => {
                const t = [{
                        id: "desc.created_at",
                        name: Jn("creationDateNewToOld"),
                        translationKey: "creationDateNewToOld",
                        sort_by: "created_at",
                        sortDirection: "desc"
                    }, {
                        id: "asc.created_at",
                        name: Jn("creationDateOldToNew"),
                        translationKey: "creationDateOldToNew",
                        sort_by: "created_at",
                        sortDirection: "asc"
                    }],
                    n = [{
                        id: "desc.created_at",
                        name: Jn("creationDateNewToOld"),
                        translationKey: "creationDateNewToOld",
                        sort_by: "created_at",
                        sortDirection: "desc"
                    }, {
                        id: "asc.created_at",
                        name: Jn("creationDateOldToNew"),
                        translationKey: "creationDateOldToNew",
                        sort_by: "created_at",
                        sortDirection: "asc"
                    }, {
                        id: "desc.date_time",
                        name: Jn("scheduledTimeNewToOld"),
                        translationKey: "scheduledTimeNewToOld",
                        sort_by: "date_time",
                        sortDirection: "desc"
                    }, {
                        id: "asc.date_time",
                        name: Jn("scheduledTimeOldToNew"),
                        translationKey: "scheduledTimeOldToNew",
                        sort_by: "date_time",
                        sortDirection: "asc"
                    }],
                    r = Number(e) === fU.homeServices ? n : t;
                return Promise.resolve(r)
            })(h?.[yU.CATEGORY]),
            isPaginated: !1,
            refetchKey: `${NH}.${h?.[yU.CATEGORY]}.${h?.[yU.SORT_BY]}.${h?.[yU.SORT]}`,
            isMultiSelect: !1,
            sx: t4.selectField,
            placeholder: n("requestsFilters.sort"),
            title: n("requestsFilters.sort"),
            searchPlaceholder: n("contacts.searchPlaceholder"),
            noDataTitle: n("requestsFilters.noData"),
            noDataDescription: n("requestsFilters.noData"),
            rightRadioInput: !1
        }, {
            formFieldName: yU.STATUS,
            value: h?.[yU.STATUS] || [],
            setSelectedValue: e => {
                e && x({
                    ...h,
                    [yU.STATUS]: e
                })
            },
            CustomRowItem: X3,
            LeftIcon: KN.FilterLineIcon,
            RightIcon: KN.CloseFill,
            onRightIconClick: () => {
                x({
                    ...h,
                    [yU.STATUS]: void 0
                })
            },
            isOpen: i,
            setIsOpen: o,
            fetcher: () => _o({
                isHistory: t,
                type: "request",
                categoryId: e
            }),
            isPaginated: !1,
            refetchKey: `${NH}${t?" history":""}`,
            isMultiSelect: !0,
            showChosenItems: !1,
            sx: t4.selectField,
            placeholder: n("requestsFilters.status"),
            title: n("requestsFilters.status"),
            searchPlaceholder: n("contacts.searchPlaceholder"),
            noDataTitle: n("requestsFilters.noData"),
            noDataDescription: n("requestsFilters.noData"),
            rightRadioInput: !1
        }, {
            formFieldName: yU.COMMUNITY,
            value: h?.[yU.COMMUNITY] || [],
            setSelectedValue: e => {
                x({
                    ...h,
                    [yU.COMMUNITY]: e
                })
            },
            CustomRowItem: X3,
            LeftIcon: KN.FilterLineIcon,
            RightIcon: KN.CloseFill,
            onRightIconClick: () => {
                x({
                    ...h,
                    [yU.COMMUNITY]: []
                })
            },
            isOpen: s,
            setIsOpen: l,
            fetcher: K3,
            refetchKey: `${NH}.${JI}`,
            isMultiSelect: !1,
            sx: t4.selectField,
            placeholder: n("requestsFilters.community"),
            title: n("requestsFilters.community"),
            searchPlaceholder: n("contacts.searchPlaceholder"),
            noDataTitle: n("requestsFilters.noData"),
            noDataDescription: n("requestsFilters.noData"),
            rightRadioInput: !1
        }, {
            formFieldName: yU.SUB_CATEGORY,
            value: h?.[yU.SUB_CATEGORY] || [],
            setSelectedValue: e => {
                x({
                    ...h,
                    [yU.SUB_CATEGORY]: e,
                    [yU.TYPE]: []
                })
            },
            CustomRowItem: X3,
            LeftIcon: KN.FilterLineIcon,
            RightIcon: KN.CloseFill,
            onRightIconClick: () => {
                x({
                    ...h,
                    [yU.SUB_CATEGORY]: [],
                    [yU.TYPE]: []
                })
            },
            isOpen: d,
            setIsOpen: c,
            fetcher: ({
                search: e
            }) => (async (e, t) => (await lo("/api-management/rf/users/requests/sub-categories?has_types=true&category_id=" + e, {
                search: t.search,
                is_active: t.is_active
            })).data)(h?.[yU.CATEGORY], {
                search: e,
                is_active: 0
            }),
            isPaginated: !1,
            refetchKey: `${NH}.${h?.[yU.CATEGORY]}`,
            isMultiSelect: !1,
            sx: t4.selectField,
            placeholder: n("requestsFilters.subcategory"),
            title: n("requestsFilters.subcategory"),
            searchPlaceholder: n("contacts.searchPlaceholder"),
            noDataTitle: n("requestsFilters.noData"),
            noDataDescription: n("requestsFilters.noData"),
            rightRadioInput: !1,
            staleTime: 0
        }, {
            formFieldName: yU.TYPE,
            value: h?.[yU.TYPE] || [],
            setSelectedValue: e => {
                x({
                    ...h,
                    [yU.TYPE]: e
                })
            },
            CustomRowItem: X3,
            LeftIcon: KN.FilterLineIcon,
            RightIcon: KN.CloseFill,
            onRightIconClick: () => {