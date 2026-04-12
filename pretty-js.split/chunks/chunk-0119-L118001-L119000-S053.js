    return e.jsxs(cP, {
        pl: "8px",
        children: [e.jsx(rP, {
            s: "24",
            mt: "16px",
            children: y("listedCommunities")
        }), e.jsx(rP, {
            s: "14",
            mb: "16px",
            children: i?.total ? `${y("common.total")} : ${i.total}` : ""
        }), e.jsx(sP, {
            justifyContent: "center",
            sx: {
                flexDirection: "column",
                mt: "32px",
                "& .MuiContainer-root": {
                    pl: "0 !important"
                }
            },
            maxWidth: "1600px",
            children: e.jsx(o7, {
                RenderTable: e.jsx(Ne, {
                    sx: {
                        backgroundColor: "white",
                        "& .MuiPaper-root": {
                            boxShadow: "none"
                        }
                    },
                    children: e.jsx(E5, {
                        isLoading: n,
                        isEmpty: !t?.length,
                        showEmptyPlaceholder: !1,
                        filters: e.jsx(cP, {
                            sx: {
                                "& fieldset": {
                                    border: "none"
                                },
                                "& input": {
                                    py: "6px"
                                }
                            },
                            children: e.jsx(RQ, {
                                search: r,
                                handleSearch: a
                            })
                        }),
                        headerData: [y("leasing.community"), y("leaseForm.city"), y("leaseForm.district"), y("no.Units"), y("no.Interests"), y("listedFor"), e.jsxs(rP, {
                            s: "14",
                            children: [y("listingStatus"), e.jsx(Ete, {
                                placement: "ar" === v ? "left" : "right",
                                title: y("marketplace.comingSoon_tooltip"),
                                sx: {
                                    position: "absolute",
                                    top: "10px",
                                    right: "10px"
                                },
                                arrow: !0,
                                children: e.jsx(w, {
                                    size: "small",
                                    sx: {
                                        ml: "4px"
                                    },
                                    children: e.jsx(z8, {
                                        color: _.palette.primary.main
                                    })
                                })
                            })]
                        }), ""],
                        pagination: ni.can(qI.Create, $I.Listings) ? e.jsx(Vte, {}) : null,
                        bottomPagination: i?.last_page > 1 && e.jsx(cP, {
                            sx: {
                                py: "1rem",
                                "& .MuiPagination-ul li button": {
                                    border: "none",
                                    backgroundColor: "#F0F0F0",
                                    borderRadius: "8px",
                                    mr: "8px"
                                },
                                "& .MuiPagination-ul li .Mui-selected": {
                                    backgroundColor: "#2E3032",
                                    color: "white",
                                    borderRadius: "8px"
                                }
                            },
                            children: e.jsx(HQ, {
                                page: o,
                                count: i?.last_page,
                                handler: l
                            })
                        }),
                        children: t?.map(t => e.jsx(Ste, {
                            data: t,
                            unList: () => c(t?.id),
                            isUnlisting: p,
                            showInterests: () => g(t?.id)
                        }, t.id))
                    })
                })
            })
        }), e.jsx(QW, {
            content: {
                title: y("marketplace.unlistCommunity"),
                body: y("marketplace.removeListingBody"),
                errors: [],
                actionText: y("common.close")
            },
            onDialogClose: () => c(null),
            isOpen: !!d,
            clickAction: () => c(null),
            primaryButton: {
                title: y("common.yes"),
                handleClick: () => u(d)
            }
        }), e.jsx(Lte, {
            communityId: f,
            open: !!f,
            onClose: () => {
                g(null)
            }
        })]
    })
}

function Vte() {
    const t = Ft(),
        [n, r] = Dt.useState(null),
        [a, i] = Dt.useState(null),
        o = Boolean(n),
        s = e => {
            r(null), i("string" == typeof e ? e : null)
        },
        {
            t: l
        } = Gn();
    return e.jsxs(cP, {
        row: !0,
        ycenter: !0,
        children: [e.jsx(dP, {
            sx: {
                mx: 2,
                my: 2,
                width: "180px",
                color: "white"
            },
            onClick: e => {
                r(e.currentTarget)
            },
            variant: "contained",
            endIcon: e.jsx(Bf, {
                color: "inherit"
            }),
            children: l("marketplace.listCommunity")
        }), e.jsxs(tt, {
            id: "basic-menu",
            anchorEl: n,
            open: o,
            onClose: s,
            sx: {
                "& .MuiPaper-root": {
                    width: "200px",
                    borderRadius: "8px",
                    border: "1px solid #ccc",
                    boxShadow: "none",
                    mt: "3px"
                },
                "& .MuiList-root": {
                    padding: 0
                }
            },
            MenuListProps: {
                "aria-labelledby": "basic-button"
            },
            children: [e.jsx(H, {
                onClick: () => {
                    s("sale")
                },
                children: l("marketplace.sellingOption")
            }), e.jsx(H, {
                onClick: () => t("/marketplace/listing/off-plan-sale-form"),
                children: l("offPlanSales")
            }), e.jsx(H, {
                onClick: () => {
                    s("rent")
                },
                children: l("marketplace.rentingOption")
            })]
        }), e.jsx(jte, {
            title: l("marketplace.listCommunity"),
            fetcher: k9,
            isOpen: !!a,
            onClose: () => i(null),
            listingType: a
        })]
    })
}

function Ate({
    filters: t,
    handleFilter: n,
    handleReset: r,
    title: a,
    selectedFilter: i
}) {
    const {
        t: o
    } = Gn(), [s, l] = Vt.useState(!1), [d, c] = Dt.useState(a), [u, p] = Dt.useState(i);
    Dt.useEffect(() => {
        p(i);
        const e = t?.find(e => e.id === i)?.name;
        c(e ? o(e) : a)
    }, [i]);
    const h = e => {
        l(!1), r?.(), e && (p(null), c(a))
    };
    return e.jsxs(ap, {
        row: !0,
        ycenter: !0,
        children: [e.jsx(r$, {
            onClick: () => {
                l(!0)
            },
            style: {
                backgroundColor: "#fff",
                minWidth: "170px",
                color: "#232425",
                height: "43px",
                border: "1px solid #E3E3E3 ",
                boxShadow: "none",
                whiteSpace: "nowrap"
            },
            children: e.jsxs(e.Fragment, {
                children: [e.jsx(Np, {
                    sx: {
                        color: "#232425",
                        mr: 4
                    }
                }), e.jsx(hp, {
                    variant: "caption",
                    bold: !0,
                    children: d
                }), e.jsx(zp, {})]
            })
        }), e.jsxs(v, {
            onClose: h,
            open: s,
            sx: {
                minWidth: "400px"
            },
            children: [e.jsx(b, {
                sx: {
                    minWidth: "400px"
                },
                children: e.jsxs(sP, {
                    justifyContent: "space-between",
                    alignItems: "center",
                    children: [e.jsx(hp, {
                        variant: "h5",
                        children: a
                    }), e.jsx(w, {
                        "aria-label": "close",
                        onClick: () => h(),
                        sx: {
                            position: "absolute",
                            right: 8,
                            top: 8,
                            color: "#000"
                        },
                        children: e.jsx(ph, {})
                    })]
                })
            }), e.jsx(ap, {
                p: "16px 24px",
                children: t?.length ? t?.map(t => e.jsxs(ap, {
                    sx: {
                        display: "flex",
                        justifyContent: "space-between",
                        alignItems: "center",
                        borderRadius: "8px",
                        p: "4px",
                        width: "100%",
                        "&:hover": {
                            cursor: "pointer",
                            backgroundColor: "#f2f2f2"
                        }
                    },
                    onClick: () => p(t?.id),
                    children: [e.jsx(hp, {
                        sx: {
                            margin: "0px",
                            fontWeight: "400 !important"
                        },
                        variant: "body",
                        children: o(t?.name)
                    }), e.jsx(C, {
                        checked: u === t?.id,
                        onChange: () => {
                            p(t?.id)
                        },
                        name: t?.name,
                        value: t?.id,
                        size: "small"
                    })]
                })) : e.jsx(ap, {
                    center: !0,
                    p: "16px",
                    children: e.jsx(hp, {
                        variant: "body",
                        children: o("common.NoDataAvailable")
                    })
                })
            }), e.jsxs(M, {
                sx: {
                    display: "flex",
                    justifyContent: "space-between",
                    borderTop: "1px solid #E3E3E3",
                    padding: "16px 24px"
                },
                children: [e.jsx(wp, {
                    onClick: () => h(!0),
                    variant: "text",
                    color: "error",
                    sx: {
                        width: "200px",
                        height: "52px"
                    },
                    disabled: !t?.length,
                    children: o("common.clear")
                }), e.jsx(wp, {
                    onClick: () => {
                        l(!1), n(u), c(t?.find(e => e.id === u)?.name)
                    },
                    variant: "hovered",
                    disabled: !u || !t?.length,
                    sx: {
                        width: "200px",
                        height: "52px"
                    },
                    children: o("common.apply")
                })]
            })]
        })]
    })
}
const Ote = async ({
    search: e,
    page: t,
    sort: n,
    communityId: r,
    is_paginate: a
}) => {
    try {
        const o = await lo("/api-management/rf/buildings", {
            is_paginate: a ?? "1",
            rf_community_id: r,
            sortBy: n.sortBy || "created_at",
            sortDirection: n.value || "desc",
            page: t?.toString(),
            search: e
        });
        return i = o, {
            list: i?.data?.list?.map(e => ({
                id: e.id,
                name: e.name,
                communityId: e.community?.id,
                communityName: e.community?.name,
                city: e.city?.name,
                district: e.district?.name,
                units: parseInt(e?.units_count || "0"),
                location: {
                    latitude: e?.map?.latitude,
                    longitude: e.map?.longitude,
                    formattedAddress: e.map?.formatted_address,
                    mapsLink: e.map?.mapsLink
                },
                yearBuilt: e.year_build
            })),
            total: i?.data?.paginator?.total,
            pageCount: i?.data?.paginator?.last_page
        }
    } catch (o) {
        throw o
    }
    var i
}, Pte = async e => {
    try {
        const n = await lo(`/api-management/rf/buildings/${e}`);
        return t = n, {
            id: t?.data?.id,
            name: t?.data?.name,
            communityName: t?.data?.community?.name,
            city: t?.data?.city?.name,
            district: t?.data?.district?.name,
            units: t?.data?.units,
            floors: parseInt(t?.data?.no_floors),
            yearBuilt: t?.data?.year_build,
            location: {
                latitude: t?.data?.map?.latitude,
                longitude: t?.data?.map?.longitude,
                formattedAddress: t?.data?.map?.formattedAddress,
                mapsLink: t?.data?.map?.mapsLink
            },
            images: t?.data?.images,
            documents: t?.data?.documents
        }
    } catch (n) {
        throw n
    }
    var t
}, Ite = async e => {
    const t = await lo(`/api-management/rf/buildings?rf_community_id=${e}`);
    return t.data?.map(({
        id: e,
        name: t
    }) => ({
        id: e,
        name: t
    }))
};

function Fte() {
    const [t, n] = Dt.useState({
        search: "",
        page: 1,
        withMissingData: null,
        buildingId: null,
        status: null,
        category_id: null
    }), [r, a] = Dt.useState(a9.UNLISTED), [i, o] = Dt.useState(null), [l, d] = Dt.useState(null), [c, u] = Dt.useState(null), [p, h] = Dt.useState(!1), [m, f] = Dt.useState(!1), [g, y] = Dt.useState(null), {
        id: v
    } = qt(), [_] = $t(), x = Ys(), b = _.get("listFor"), w = "rent" === b, C = "1" === _.get("isOfPlan"), {
        t: M
    } = Gn(), {
        listedUnitsQuery: {
            list: S,
            isLoading: L,
            metadata: k
        },
        nonListedUnitsQuery: {
            list: T,
            isLoading: j,
            metadata: E,
            missingUnits: D,
            unMissingData: V
        },
        buildings: A,
        unitsStats: O,
        unlistAllMutation: P,
        unlistUnitMutation: I,
        listAllMutation: F,
        listUnitMutation: H,
        editUnitMutation: N
    } = function({
        activeTab: e,
        communityId: t,
        fetchParams: n
    }) {
        const {
            data: r,
            isLoading: a
        } = tl([LH, n, t], async () => await B9({
            listed: !0,
            communityId: t,
            ...n,
            withMissingData: void 0,
            category_id: n.category_id
        }), {
            useErrorBoundary: !1,
            enabled: !(e !== a9.LISTED || !t)
        }), {
            data: i,
            isLoading: o
        } = tl([kH, n, t], async () => await B9({
            listed: !1,
            communityId: t,
            ...n,
            category_id: n.category_id
        }), {
            useErrorBoundary: !1,
            enabled: e === a9.UNLISTED
        }), {
            data: s
        } = tl([eF, t], async () => await Ote({
            search: "",
            sort: {
                sortBy: "",
                value: ""
            },
            communityId: t
        }), {
            useErrorBoundary: !1
        }), {
            data: l
        } = tl([TH, t], async () => await (async e => {
            try {
                const t = await lo(`/api-management/marketplace/admin/units/statistic/${e}`);
                return {
                    listed: t?.data?.no_listed_unit,
                    unlisted: t?.data?.no_unlisted_unit
                }
            } catch (t) {
                throw t
            }
        })(t), {
            useErrorBoundary: !1
        }), d = Ys(), {
            t: c
        } = Gn(), u = nl({
            mutationFn: async ({
                communityId: e,
                buildingId: t,
                status: n,
                search: r
            }) => z9({
                communityId: e,
                buildingId: t,
                status: n,
                isListing: !1,
                search: r
            }),
            onSuccess: () => {
                d.invalidateQueries(), Zi.success(c("marketplace.unListAllSuccess"))
            }
        }), p = nl({
            mutationFn: async ({
                unitId: e
            }) => z9({
                unitId: e,
                isListing: !1,
                communityId: t
            }),
            onSuccess: () => {
                d.invalidateQueries()
            }
        }), h = nl({
            mutationFn: async ({
                communityId: e,
                buildingId: t,
                status: n,
                search: r,
                category_id: a
            }) => z9({
                communityId: e,
                buildingId: t,
                status: n,
                isListing: !0,
                search: r,
                category_id: a
            }),
            onSuccess: () => {
                d.invalidateQueries()
            }
        }), m = nl({
            mutationFn: async ({
                unitId: e
            }) => z9({
                unitId: e,
                isListing: !0,
                communityId: t
            }),
            onSuccess: () => {
                d.invalidateQueries([TH, t]), d.invalidateQueries([LH, n, t]), d.invalidateQueries([kH, n, t]), d.invalidateQueries([SH, "", 1]), Zi.success(c("marketplace.listUnitSuccess"))
            }
        }), f = nl({
            mutationFn: async ({
                unitId: e,
                data: t
            }) => (async (e, t) => {
                try {
                    const r = (n = t, {
                        ...n,
                        unit_area: n.area,
                        area: void 0
                    });
                    await co(`/api-management/marketplace/admin/units/${e}`, r)
                } catch (r) {
                    throw r
                }
                var n
            })(e, t),
            onSuccess: () => {
                d.invalidateQueries([TH, t]), d.invalidateQueries([LH, n, t]), d.invalidateQueries([kH, n, t]), Zi.success(c("marketplace.editUnitSuccess"))
            }
        });
        return {
            listedUnitsQuery: {
                list: r?.list,
                metadata: r?.metadata,
                isLoading: a
            },
            nonListedUnitsQuery: {
                list: i?.list,
                metadata: i?.metadata,
                isLoading: o,
                missingUnits: i?.noUnitsMissingData,
                unMissingData: i?.noUnitsUnMissingData
            },
            buildings: s?.list,
            unitsStats: l,
            unlistAllMutation: u,
            unlistUnitMutation: p,
            listAllMutation: h,
            listUnitMutation: m,
            editUnitMutation: f
        }
    }({
        activeTab: r,
        communityId: v,
        fetchParams: t
    }), [R, Y] = Dt.useState(0);
    Dt.useEffect(() => {
        a(C ? a9.OFF_PLAN : a9.SALE_INFORMATION)
    }, [C]);
    const B = e => {
            n({
                ...t,
                status: e,
                page: 1
            })
        },
        z = e => {
            n({
                ...t,
                category_id: e,
                page: 1
            })
        },
        U = e => {
            n({
                ...t,
                buildingId: e,
                page: 1
            })
        },
        W = e => {
            n({
                ...t,
                withMissingData: e,
                page: 1
            })
        },
        Z = async () => {
            h(e => !e);
            try {
                f(!0), await (async (e, t) => await co(`/api-management/marketplace/admin/units/prices-visibility/all/${e}`, {
                    action: t
                }))(v, p ? "hide" : "show"), await x.invalidateQueries({
                    queryKey: [kH]
                }), Zi.success(M("unitsVisibilityToggledSuccessfully"))
            } catch (e) {
                Zi.error(M("unitsVisibilityToggledError"))
            } finally {
                f(!1)
            }
        }, q = async e => {
            try {
                y(parseInt(e)), await (async e => await co(`/api-management/marketplace/admin/units/prices-visibility/${e}`))(e), await x.invalidateQueries({
                    queryKey: [kH]
                }), Zi.success(M("unitVisibilityToggledSuccessfully"))
            } catch (t) {
                Zi.error(M("unitVisibilityToggledError"))
            } finally {
                y(null)
            }
        }, {
            CurrentBrand: $
        } = Gc(), G = !!qc[$]?.enableContractsBooking, K = s(), Q = {
            [a9.LISTED]: {
                list: S,
                metadata: k,
                isLoading: L,
                filters: [{
                    title: M("Unit Status"),
                    filters: n9(b, G),
                    handleFilter: B,
                    selectedFilter: t.status,
                    handleReset: () => B(null)
                }, {
                    title: M("buildings"),
                    filters: A,
                    handleFilter: U,
                    selectedFilter: t.buildingId,
                    handleReset: () => U(null)
                }, {
                    title: M("unitForm.unitCategoryInfo"),
                    filters: [{
                        id: "2",
                        name: "residential"
                    }, {
                        id: "3",
                        name: "commercial"
                    }],
                    handleFilter: z,
                    selectedFilter: t.category_id,
                    handleReset: () => z(null)
                }],
                note: "marketplace.listedNote",
                btnText: "marketplace.unListAll",
                btnColor: "error",
                btnDisabled: !1,
                toggleAllVisibilityBtn: () => e.jsx(e.Fragment, {}),
                toggleVisibilityBtn: w ? ({
                    isHidden: t
                }) => e.jsx(hp, {
                    variant: "caption",
                    sx: {
                        fontWeight: "400",
                        fontSize: "14px",
                        color: "#232425"
                    },
                    children: M(t ? "priceIsInvisible" : "priceIsVisible")
                }) : () => null,
                isMutationAllSuccess: P.isSuccess,
                confirmationModalData: {
                    title: "marketplace.unlistAllNote",
                    content: "marketplace.unlistAllConfirmation",
                    color: "red",
                    data: {
                        units: k?.total
                    },
                    primaryBtnColor: "error",
                    mutate: () => {
                        P.mutate({
                            communityId: parseInt(v),
                            buildingId: t.buildingId,
                            status: t.status,
                            search: t.search
                        })
                    }
                },
                isAllMutating: P.isLoading,
                actions: ni.can(qI.Update, $I.Listings) ? [{
                    variant: "hovered",
                    startIcon: null,
                    label: "marketplace.unlist",
                    color: "error",
                    onClick: e => {
                        o(e)
                    },
                    sx: {
                        boxShadow: "none"
                    },
                    name: "unlist"
                }] : []
            },
            [a9.UNLISTED]: {
                list: T,
                metadata: E,
                isLoading: j,
                filters: [{
                    title: M("Unit Status"),
                    filters: n9(b, G),
                    handleFilter: B,
                    selectedFilter: t.status,
                    handleReset: () => B(null)
                }, {
                    title: M("buildings"),
                    filters: A,
                    handleFilter: U,
                    selectedFilter: t.buildingId,
                    handleReset: () => U(null)
                }, {
                    title: M("Units Data"),
                    filters: r9,
                    handleFilter: W,
                    selectedFilter: t.withMissingData,
                    handleReset: () => W(void 0)
                }, {
                    title: M("unitForm.unitCategoryInfo"),
                    filters: [{
                        id: "2",
                        name: "residential"
                    }, {
                        id: "3",
                        name: "commercial"
                    }],
                    handleFilter: z,
                    selectedFilter: t.category_id,
                    handleReset: () => z(null)
                }],
                note: "marketplace.unlistedNote",
                btnText: "marketplace.listAll",
                btnColor: "primary",
                btnDisabled: D === E?.total || "1" === t.withMissingData,
                toggleAllVisibilityBtn: w ? ({
                    isAllPricesVisibleLoading: t
                }) => e.jsx(wp, {
                    variant: "outlined",
                    color: "primary",
                    onClick: Z,
                    sx: {
                        fontWeight: "bold"
                    },
                    endIcon: p ? e.jsx(KN.EyeLine, {}) : e.jsx(KN.EyeOffLine, {}),
                    disabled: t,
                    isLoading: t,
                    children: M(p ? "hideAllPrices" : "showAllPrices")
                }) : () => null,
                toggleVisibilityBtn: w ? ({
                    isHidden: t,
                    id: n,
                    disabled: r
                }) => e.jsx(wp, {
                    variant: "outlined",
                    onClick: () => q(n.toString()),
                    sx: {
                        fontWeight: "400",
                        fontSize: "14px"
                    },
                    endIcon: t ? e.jsx(KN.EyeLine, {
                        color: "primary"
                    }) : e.jsx(KN.EyeOffLine, {
                        color: "primary"
                    }),
                    disabled: r,
                    isLoading: g === n,
                    children: M(t ? "showPrice" : "hidePrice")
                }) : () => null,
                confirmationModalData: {
                    title: "marketplace.listAllNote",
                    content: D && "0" !== t.withMissingData ? "marketplace.listAllConfirmationWithMissing" : "marketplace.listAllConfirmation",
                    data: {
                        units: D,
                        unitsWithoutMissing: V
                    },
                    color: K.palette.primary.main,
                    primaryBtnColor: K.palette.primary.main,
                    mutate: () => {
                        F.mutate({
                            communityId: parseInt(v),
                            buildingId: t.buildingId,
                            status: t.status,
                            search: t.search,
                            category_id: t.category_id
                        })
                    }
                },
                isMutationAllSuccess: F.isSuccess,
                isAllMutating: F.isLoading,
                actions: ni.can(qI.Update, $I.Listings) ? [{
                    variant: "text",
                    startIcon: e.jsx(d8, {}),
                    label: "edit",
                    onClick: e => {
                        u(T?.find(t => t.id === e))
                    },
                    name: "edit"
                }, {
                    variant: "contained",
                    startIcon: null,
                    label: "marketplace.list",
                    itemId: null,
                    onClick: e => {
                        H.mutate({
                            unitId: e
                        }), d(e)
                    },
                    name: "list",
                    sx: {
                        "&:disabled": {
                            backgroundColor: "transparent",
                            color: "#B6B6B6",
                            border: "1px solid #E3E3E3",
                            boxShadow: "none"
                        }
                    },
                    isLoading: e => H.isLoading && l === e,
                    disabled: H.isLoading
                }] : []
            }
        };
    return {
        id: v,
        tabStrategy: Q[r],
        activeTab: r,
        unitsStats: O,
        search: t.search,
        page: t.page,
        setActiveTab: e => {
            a(e), n({
                ...t,
                page: 1,
                status: null,
                buildingId: null,
                category_id: null,
                withMissingData: void 0,
                search: ""
            })
        },
        handleSearch: e => {
            n(t => ({
                ...t,
                search: e,
                page: 1
            }))
        },
        handlePageChange: e => {
            n({
                ...t,
                page: e
            })
        },
        unlistAll: P.mutate,
        isUnlistAllLoading: P.isLoading,
        unitToBeUnlisted: i,
        setUnitToBeUnlisted: o,
        unlistUnit: I.mutate,
        isUnlistingUnitSuccess: I.isSuccess,
        listAll: F.mutate,
        isListAllLoading: F.isLoading,
        isListAllSuccess: F.isSuccess,
        listUnit: H.mutate,
        isListUnitSuccess: H.isSuccess,
        editUnit: N.mutate,
        isEditing: N.isLoading,
        isEditUnitSuccess: N.isSuccess,
        unitToBeEdited: c,
        setUnitToBeEdited: u,
        isRent: w,
        isAllPricesVisible: p,
        isAllPricesVisibleLoading: m,
        unitIdLoadingVisibility: g
    }
}
const Hte = {
    [t9.AVAILABLE]: {
        bg: "#EDFAF4",
        txt: "#0A9458"
    },
    [t9.SOLD]: {
        bg: "#EBF0F1",
        txt: "#002A37"
    },
    [t9.LEASED]: {
        bg: "#FCEDC7",
        txt: "#8A6A16"
    },
    [t9.BOOKED]: {
        bg: "#F0F0F0",
        txt: "#4F5154"
    }
};

function Nte({
    data: t,
    actions: n,
    isRent: r,
    toggleVisibilityBtn: a
}) {
    const {
        t: i
    } = Gn(), o = !t?.area || !t?.price || !t?.deposit, s = e => e?.disabled || o && "list" === e?.name;
    return e.jsxs(uP, {
        children: [e.jsx(pP, {
            component: "td",
            scope: "row",
            children: e.jsx(hp, {
                s: "16",
                children: t?.name
            })
        }), e.jsx(pP, {
            component: "td",
            scope: "row",
            children: t?.building ?? "--"
        }), e.jsxs(pP, {
            component: "td",
            scope: "row",
            children: [e.jsx(hp, {
                s: "16",
                light: !0,
                textTransform: "capitalize",
                display: "block",
                children: t?.type
            }), e.jsx(hp, {
                s: "12",
                light: !0,
                textTransform: "capitalize",
                children: t?.subtype
            })]
        }), e.jsx(pP, {
            component: "td",
            scope: "row",
            children: t?.area ? e.jsxs(hp, {
                s: "16",
                light: !0,
                children: [t?.area, " ", i("properties.add_unit.sqm")]
            }) : e.jsx(hp, {
                s: "16",
                light: !0,
                color: "red",
                children: i("signUp.missingData")
            })
        }), r ? e.jsx(pP, {
            component: "td",
            scope: "row",
            children: t?.annualRentPrice ? e.jsx(hp, {
                s: "16",
                light: !0,
                currency: !0,
                color: r && t?.isHidden ? "#CACACA" : "black",
                children: d6(t?.annualRentPrice)