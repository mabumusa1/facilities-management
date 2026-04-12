                backgroundColor: "#F5F5F5"
            }
        },
        children: [e.jsx(hp, {
            s: 16,
            sx: {
                fontWeight: 500,
                color: "#232425"
            },
            children: t.name
        }), t.phoneNumber && e.jsx(hp, {
            s: 14,
            sx: {
                color: "#757575",
                direction: "ltr",
                textAlign: "left"
            },
            children: t.phoneNumber
        })]
    }), $ee = {
        selectField: {
            backgroundColor: "transparent",
            width: "fit-content",
            padding: "11px 16px"
        }
    }, Gee = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/, Kee = /^[a-zA-Z\u0620-\u064A\s]+$/, Qee = e => v1().shape({
        firstName: a1().trim().required(e("customers.firstNameRequired")).max(50, e("customers.firstNameMax")).matches(Kee, e("customers.firstNameInvalid")),
        lastName: a1().trim().required(e("customers.lastNameRequired")).max(50, e("customers.lastNameMax")).matches(Kee, e("customers.lastNameInvalid")),
        nationalId: a1().trim().nullable().optional().test("digits", e("customers.nationalIdDigits"), e => !e || /^\d{10}$/.test(e)).test("starts-with", e("customers.nationalIdStartsWith"), e => !e || /^[123]/.test(e)),
        nationality: a1().max(191).nullable().optional(),
        phoneCountryCode: v1().required(e("customers.countryCodeRequired")),
        phoneNumber: a1().trim().required(e("customers.phoneNumberRequired")).matches(/^[A-Za-z0-9]+$/, e("customers.phoneNumberInvalid")).test("phone-validation", e("customers.phoneNumberInvalid"), function(e) {
            return !0
        }),
        email: a1().trim().email(e("customers.emailInvalid")).max(150, e("customers.emailMax")).nullable().optional(),
        interested: a1().oneOf(["buy", "rent"], e("customers.interestedInvalid")).required(),
        status: WX().optional().nullable(),
        leadOwner: WX().optional().nullable(),
        source: WX().optional().nullable(),
        priority: WX().optional().nullable()
    });

function Jee(t) {
    const [n, r] = Dt.useState(!1), {
        t: a
    } = Gn(), i = Ft(), {
        filters: o,
        search: s,
        page: l,
        sort: d,
        selectedFilters: c,
        clearForm: p,
        isFilterApplied: h,
        setSearch: m,
        setPage: f
    } = (() => {
        const {
            t: e
        } = Gn(), [t, n] = Dt.useState(!1), [r, a] = Dt.useState(!1), [i, o] = Dt.useState(!1), [s, l] = Dt.useState(!1), [d, c] = Dt.useState(!1), [u, p] = Dt.useState(!1), {
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
            defaultFilter: Oee,
            defaultSort: Aee
        }), b = e => {
            if (!e?.sortBy) return [];
            const t = Vee.find(t => t.sort_by === e.sortBy && t.sortDirection === e.sortDirection && t.id === e.id);
            return [{
                ...e,
                translationKey: t?.translationKey || e.translationKey
            }]
        }, [w, C] = Dt.useState(b(g));
        Dt.useEffect(() => {
            C(g?.sortBy ? b(g) : [])
        }, [g?.id, g?.sortBy, g?.sortDirection]);
        const M = [{
                formFieldName: kee,
                value: w,
                setSelectedValue: e => {
                    C(e), e?.[0]?.sort_by && _({
                        id: e?.[0]?.id,
                        sortBy: e?.[0]?.sort_by,
                        name: e?.[0]?.name,
                        sortDirection: e?.[0]?.value || e?.[0]?.sortDirection
                    })
                },
                CustomRowItem: Zee,
                LeftIcon: ZN.LineHeightIcon,
                RightIcon: "desc.created_at" !== g?.id ? KN.CloseFill : void 0,
                onRightIconClick: () => {
                    _(Aee)
                },
                isOpen: t,
                setIsOpen: n,
                fetcher: () => Promise.resolve(Vee.map(t => ({
                    ...t,
                    name: e(t.translationKey)
                }))),
                isPaginated: !1,
                refetchKey: `${EH}.sort`,
                isMultiSelect: !1,
                sx: $ee.selectField,
                placeholder: e("customers.sort"),
                title: e("customers.sort"),
                searchPlaceholder: e("contacts.searchPlaceholder"),
                noDataTitle: e("customers.noData"),
                noDataDescription: e("customers.noData"),
                rightRadioInput: !1,
                hideSearchBar: !0
            }, {
                formFieldName: wee,
                value: h?.[wee] || [],
                setSelectedValue: e => {
                    x({
                        ...h,
                        [wee]: e
                    })
                },
                CustomRowItem: Zee,
                LeftIcon: KN.FilterLineIcon,
                RightIcon: KN.CloseFill,
                onRightIconClick: () => {
                    x({
                        ...h,
                        [wee]: []
                    })
                },
                isOpen: r,
                setIsOpen: a,
                fetcher: () => Promise.resolve(Tee.map(t => ({
                    ...t,
                    name: e(t.translationKey)
                }))),
                isPaginated: !1,
                refetchKey: `${EH}.status`,
                isMultiSelect: !0,
                showChosenItems: !1,
                sx: $ee.selectField,
                placeholder: e("customers.status"),
                title: e("customers.status"),
                searchPlaceholder: e("contacts.searchPlaceholder"),
                noDataTitle: e("customers.noData"),
                noDataDescription: e("customers.noData"),
                rightRadioInput: !1,
                hideSearchBar: !0
            }, {
                formFieldName: Cee,
                value: h?.[Cee] || [],
                setSelectedValue: e => {
                    x({
                        ...h,
                        [Cee]: e
                    })
                },
                CustomRowItem: Zee,
                LeftIcon: KN.FilterLineIcon,
                RightIcon: KN.CloseFill,
                onRightIconClick: () => {
                    x({
                        ...h,
                        [Cee]: []
                    })
                },
                isOpen: i,
                setIsOpen: o,
                fetcher: () => Promise.resolve(jee.map(t => ({
                    ...t,
                    name: e(t.translationKey)
                }))),
                isPaginated: !1,
                refetchKey: `${EH}.source`,
                isMultiSelect: !0,
                showChosenItems: !1,
                sx: $ee.selectField,
                placeholder: e("customers.source"),
                title: e("customers.source"),
                searchPlaceholder: e("contacts.searchPlaceholder"),
                noDataTitle: e("customers.noData"),
                noDataDescription: e("customers.noData"),
                rightRadioInput: !1,
                hideSearchBar: !0
            }, {
                formFieldName: Mee,
                value: h?.[Mee] || [],
                setSelectedValue: e => {
                    x({
                        ...h,
                        [Mee]: e
                    })
                },
                CustomRowItem: qee,
                LeftIcon: KN.FilterLineIcon,
                RightIcon: KN.CloseFill,
                onRightIconClick: () => {
                    x({
                        ...h,
                        [Mee]: []
                    })
                },
                isOpen: s,
                setIsOpen: l,
                fetcher: Wee,
                isPaginated: !0,
                refetchKey: `${EH}.assignee`,
                isMultiSelect: !1,
                showChosenItems: !0,
                sx: $ee.selectField,
                placeholder: e("customers.assignee"),
                title: e("customers.assignee"),
                searchPlaceholder: e("customers.searchAssignee"),
                noDataTitle: e("customers.noAssigneeFound"),
                noDataDescription: e("customers.noAssigneeFound"),
                rightRadioInput: !1,
                hideSearchBar: !1
            }, {
                formFieldName: See,
                value: h?.[See] || [],
                setSelectedValue: e => {
                    x({
                        ...h,
                        [See]: e
                    })
                },
                CustomRowItem: Zee,
                LeftIcon: KN.FilterLineIcon,
                RightIcon: KN.CloseFill,
                onRightIconClick: () => {
                    x({
                        ...h,
                        [See]: []
                    })
                },
                isOpen: d,
                setIsOpen: c,
                fetcher: () => Promise.resolve(Eee.map(t => ({
                    ...t,
                    name: e(t.translationKey)
                }))),
                isPaginated: !1,
                refetchKey: `${EH}.priority`,
                isMultiSelect: !1,
                showChosenItems: !0,
                sx: $ee.selectField,
                placeholder: e("customers.priority"),
                title: e("customers.priority"),
                searchPlaceholder: e("contacts.searchPlaceholder"),
                noDataTitle: e("customers.noData"),
                noDataDescription: e("customers.noData"),
                rightRadioInput: !1,
                hideSearchBar: !0
            }, {
                formFieldName: Lee,
                value: h?.[Lee] || [],
                setSelectedValue: e => {
                    x({
                        ...h,
                        [Lee]: e
                    })
                },
                CustomRowItem: Zee,
                LeftIcon: KN.FilterLineIcon,
                RightIcon: KN.CloseFill,
                onRightIconClick: () => {
                    x({
                        ...h,
                        [Lee]: []
                    })
                },
                isOpen: u,
                setIsOpen: p,
                fetcher: () => Promise.resolve(Dee.map(t => ({
                    ...t,
                    name: e(t.translationKey)
                }))),
                isPaginated: !1,
                refetchKey: `${EH}.type`,
                isMultiSelect: !1,
                showChosenItems: !0,
                sx: $ee.selectField,
                placeholder: e("customers.type"),
                title: e("customers.type"),
                searchPlaceholder: e("contacts.searchPlaceholder"),
                noDataTitle: e("customers.noData"),
                noDataDescription: e("customers.noData"),
                rightRadioInput: !1,
                hideSearchBar: !0
            }],
            S = [m && 1 !== m, f && "" !== f, g?.sortBy && "desc.created_at" !== g?.id, !!h?.[wee]?.length, !!h?.[Cee]?.length, !!h?.[Mee]?.length, !!h?.[See]?.length, !!h?.[Lee]?.length]?.some(Boolean);
        return {
            filters: M,
            search: f,
            page: m,
            sort: g,
            selectedFilters: h,
            clearForm: () => {
                x(Oee), y(""), v(1), _(Aee), C([])
            },
            isFilterApplied: S,
            setSearch: y,
            setPage: v
        }
    })(), {
        data: g,
        isLoading: y,
        refetch: v
    } = tl([jH, {
        page: l,
        search: s,
        filter: c,
        sort: d
    }], async () => await F9({
        page: l,
        search: s,
        filter: c,
        sort: d
    }), {
        useErrorBoundary: !1
    }), _ = bf({
        defaultValues: {
            firstName: "",
            lastName: "",
            nationalId: null,
            nationality: null,
            email: null,
            phoneNumber: "",
            phoneCountryCode: {
                id: "SA",
                name: "(+966)"
            },
            interested: "buy",
            status: null,
            leadOwner: null,
            source: null,
            priority: null
        },
        mode: "onChange",
        reValidateMode: "onChange",
        resolver: L1(Qee(a))
    }), {
        handleSubmit: x,
        formState: {
            errors: b
        },
        control: w,
        trigger: C,
        reset: M
    } = _, S = () => {
        M(), r(!1)
    }, {
        isLoading: L,
        mutate: k,
        isError: T
    } = nl({
        mutationFn: () => R9(_.getValues()),
        retry: !1,
        onSuccess: () => {
            Zi.success(a("customers.customerCreated"), {
                toastId: "createCustomerSuccess"
            }), v(), S(), t?.()
        },
        onError: () => {}
    }), j = Dt.useMemo(() => Tee.map(e => ({
        id: e.id,
        name: a(e.translationKey)
    })), [a]), E = Dt.useMemo(() => jee.map(e => ({
        id: e.id,
        name: a(e.translationKey)
    })), [a]), D = Dt.useMemo(() => Eee.map(e => ({
        id: e.id,
        name: a(e.translationKey)
    })), [a]), V = Dt.useMemo(() => {
        const t = [{
            key: "name",
            header: a("customers.name"),
            render: t => e.jsx(rP, {
                variant: "h6",
                sx: {
                    minWidth: "150px",
                    whiteSpace: "nowrap"
                },
                children: t.name
            })
        }, {
            key: "phoneNumber",
            header: a("customers.phone"),
            dir: "ltr",
            render: e => e.phoneNumber || "--"
        }, {
            key: "email",
            header: a("customers.email"),
            render: e => e.email || "--"
        }, {
            key: "interested",
            header: a("customers.interest"),
            render: e => e.interested ? a(`customers.${e.interested.toLowerCase()}`) : "--"
        }, {
            key: "status",
            header: a("customers.status"),
            render: t => {
                const n = (() => {
                    if (t.status) {
                        const e = j.find(e => String(e.id) === String(t.status));
                        return e ? e.name : t.status
                    }
                    return "--"
                })();
                return e.jsx(cP, {
                    sx: {
                        whiteSpace: "nowrap"
                    },
                    children: n
                })
            }
        }, {
            key: "leadOwner",
            header: a("customers.leadOwner"),
            render: t => t.leadOwner?.name ? e.jsxs(cP, {
                sx: {
                    display: "flex",
                    flexDirection: "column",
                    whiteSpace: "nowrap"
                },
                children: [e.jsx(rP, {
                    variant: "body2",
                    children: t.leadOwner.name
                }), e.jsx(rP, {
                    variant: "caption",
                    sx: {
                        color: "text.secondary"
                    },
                    dir: "ltr",
                    children: t.leadOwner.phoneNumber
                })]
            }) : a("customers.noAssignee")
        }, {
            key: "source",
            header: a("customers.source"),
            render: e => {
                if (e.source) {
                    const t = E.find(t => String(t.id) === String(e.source));
                    return t ? t.name : e.source
                }
                return "--"
            }
        }, {
            key: "priority",
            header: a("customers.priority"),
            render: e => {
                if (e.priority) {
                    const t = D.find(t => t.id === e.priority || t.name?.toLowerCase() === e.priority?.toLowerCase());
                    return t ? t.name : e.priority
                }
                return "--"
            }
        }, {
            key: "date",
            header: a("customers.date"),
            dir: "ltr",
            render: e => e.date ? tR(e.date).format("DD/MM/YYYY hh:mm A") : "--"
        }, {
            key: "lastModified",
            header: a("customers.lastModified"),
            dir: "ltr",
            render: e => {
                const t = e.lastModified || e.date;
                return t ? tR(t).format("DD/MM/YYYY hh:mm A") : "--"
            }
        }, {
            key: "lastContact",
            header: a("customers.lastContact"),
            dir: "ltr",
            render: e => e.lastContact ? tR(e.lastContact).format("D/M/YYYY") : "--"
        }, {
            key: "customerType",
            header: a("customers.customerType"),
            render: e => {
                if (e.role) {
                    const t = {
                        Lead: "customers.leads",
                        Owner: "customers.owners",
                        Tenant: "customers.tenants"
                    } [e.role];
                    return t ? a(t) : e.role
                }
                return "--"
            }
        }, {
            key: "viewDetails",
            header: "",
            render: t => e.jsx(dP, {
                sx: {
                    backgroundColor: e => u(e.palette.primary.main, .1),
                    color: e => e.palette.primary.main,
                    width: "130px",
                    height: "46px",
                    fontSize: "12px",
                    px: "16px",
                    "&:hover": {
                        backgroundColor: e => `${e.palette.primary.main}8`,
                        color: e => e.palette.primary.main
                    }
                },
                onClick: e => {
                    e.stopPropagation(), i(`/marketplace/customers/${t.id}`)
                },
                children: a("viewDetails")
            })
        }];
        return {
            columns: t,
            noDataTitle: a("customers.noDataAvailable_title"),
            noDataBody: a("customers.noDataAvailable_body")
        }
    }, [a, i, j, E, D]);
    return {
        list: g?.list,
        total: g?.total,
        pagesCount: g?.pagesCount,
        search: s,
        page: l,
        sort: d,
        selectedFilters: c,
        isLoading: y,
        setSearch: m,
        setPage: f,
        filters: o,
        clearForm: p,
        isFilterApplied: h,
        form: _,
        submitHandler: async () => {
            await C() && x(() => k())()
        },
        isError: T,
        errors: b,
        control: w,
        reset: M,
        isCreating: L,
        isCreateFormOpen: n,
        setIsCreateFormOpen: r,
        handleClose: S,
        tableConfig: V
    }
}
const Xee = ({
    open: t = !1,
    handleClose: n = () => {}
}) => {
    const {
        t: r
    } = Gn(), {
        form: i,
        submitHandler: o,
        isCreating: s,
        errors: l,
        control: d,
        handleClose: c
    } = Jee(n), u = () => {
        c(), n()
    }, p = Object.keys(l).length > 0, h = [{
        label: r("customers.buying"),
        value: "buy"
    }, {
        label: r("customers.renting"),
        value: "rent"
    }], {
        data: m = []
    } = tl({
        queryKey: ["assigneeList"],
        queryFn: () => (async e => {
            try {
                const t = await lo("/api-management/rf/admins", {
                    roles: [uU.AccountAdmin, uU.Admin, pU.Leasing, pU.Marketing],
                    query: e?.search || "",
                    page: e?.page || 1,
                    sort_dir: "latest",
                    active: 1,
                    is_paginate: 0
                });
                return O$(t)
            } catch (t) {
                throw t
            }
        })(),
        enabled: t
    }), f = Tee.map(e => ({
        id: e.id,
        name: r(e.translationKey)
    })), y = jee.map(e => ({
        id: e.id,
        name: r(e.translationKey)
    })), x = Eee.map(e => ({
        id: e.id,
        name: r(e.translationKey)
    })), b = m.map(e => ({
        id: e.id,
        name: e.name,
        phoneNumber: e.phoneNumber
    }));
    return e.jsx(v, {
        open: t,
        onClose: u,
        maxWidth: "lg",
        fullWidth: !0,
        children: e.jsxs(_, {
            sx: {
                padding: "24px"
            },
            children: [e.jsxs(a, {
                component: "header",
                sx: {
                    display: "flex",
                    justifyContent: "space-between",
                    alignItems: "center",
                    marginBottom: "24px"
                },
                children: [e.jsx(rP, {
                    sx: {
                        margin: "8px 0"
                    },
                    s: 26,
                    children: r("customers.createNewLead")
                }), e.jsx(w, {
                    "aria-label": "close",
                    onClick: u,
                    children: e.jsx(ph, {})
                })]
            }), e.jsxs(g, {
                container: !0,
                spacing: 4,
                children: [e.jsx(g, {
                    item: !0,
                    xs: 12,
                    md: 6,
                    children: e.jsx(o$, {
                        label: r("contact.fname") + "*",
                        placeholder: r("customers.enterFirstName"),
                        name: "firstName",
                        errors: l,
                        control: d,
                        style: {
                            margin: "15px 0px"
                        },
                        onKeyPress: e => {
                            e.key.match(/[0-9]/g) && e.preventDefault()
                        },
                        rules: {
                            required: !0,
                            pattern: /^[^0-9]*$/
                        }
                    })
                }), e.jsx(g, {
                    item: !0,
                    xs: 12,
                    md: 6,
                    children: e.jsx(o$, {
                        label: r("customers.lastName") + "*",
                        placeholder: r("customers.enterLastName"),
                        name: "lastName",
                        errors: l,
                        control: d,
                        style: {
                            margin: "15px 0px"
                        },
                        onKeyPress: e => {
                            e.key.match(/[0-9]/g) && e.preventDefault()
                        },
                        rules: {
                            required: !0,
                            pattern: /^[^0-9]*$/
                        }
                    })
                }), e.jsx(g, {
                    item: !0,
                    xs: 12,
                    md: 6,
                    children: e.jsx(o$, {
                        label: r("signUp.nationalId"),
                        name: "nationalId",
                        placeholder: r("contacts.national_id_p"),
                        errors: l,
                        control: d,
                        style: {
                            margin: "15px 0px"
                        },
                        rules: {
                            required: !1,
                            pattern: /^\d{10}$/,
                            validate: e => !e || (/^[123]/.test(e) || r("customers.nationalIdStartsWith"))
                        }
                    })
                }), e.jsx(g, {
                    item: !0,
                    xs: 12,
                    md: 6,
                    children: e.jsx(km, {
                        ...i,
                        children: e.jsx(a, {
                            sx: {
                                "& #nationality": {
                                    textAlign: "left"
                                }
                            },
                            children: e.jsx(Uee, {
                                name: "nationality"
                            })
                        })
                    })
                }), e.jsx(g, {
                    item: !0,
                    xs: 12,
                    md: 6,
                    children: e.jsx(x0, {
                        requiredLabel: !0,
                        phoneNumberName: "phoneNumber",
                        phoneCountryCodeName: "phoneCountryCode",
                        form: i,
                        rules: {
                            required: !0
                        },
                        isObject: !0,
                        columnSizes: {
                            codeField: 3.5,
                            phoneField: 8.5
                        }
                    })
                }), e.jsx(g, {
                    item: !0,
                    xs: 12,
                    md: 6,
                    children: e.jsx(o$, {
                        label: r("customers.emailId"),
                        name: "email",
                        placeholder: r("customers.enterEmailId"),
                        errors: l,
                        control: d,
                        style: {
                            margin: "15px 0"
                        },
                        rules: {
                            required: !1,
                            pattern: Gee
                        }
                    })
                }), e.jsx(g, {
                    item: !0,
                    xs: 12,
                    md: 6,
                    children: e.jsx(oq, {
                        name: "status",
                        control: d,
                        label: r("customers.status"),
                        placeholder: r("customers.selectStatus"),
                        options: f,
                        valueIsObject: !0,
                        errors: l,
                        sx: {
                            margin: "15px 0px"
                        }
                    })
                }), e.jsx(g, {
                    item: !0,
                    xs: 12,
                    md: 6,
                    children: e.jsx(oq, {
                        name: "leadOwner",
                        control: d,
                        label: r("customers.leadOwner"),
                        placeholder: r("customers.selectLeadOwner"),
                        options: b,
                        valueIsObject: !0,
                        errors: l,
                        sx: {
                            margin: "15px 0px"
                        }
                    })
                }), e.jsx(g, {
                    item: !0,
                    xs: 12,
                    md: 6,
                    children: e.jsx(oq, {
                        name: "source",
                        control: d,
                        label: r("customers.source"),
                        placeholder: r("customers.selectSource"),
                        options: y,
                        valueIsObject: !0,
                        errors: l,
                        sx: {
                            margin: "15px 0px"
                        }
                    })
                }), e.jsx(g, {
                    item: !0,
                    xs: 12,
                    md: 6,
                    children: e.jsx(oq, {
                        name: "priority",
                        control: d,
                        label: r("customers.priority"),
                        placeholder: r("customers.selectPriority"),
                        options: x,
                        valueIsObject: !0,
                        errors: l,
                        sx: {
                            margin: "15px 0px"
                        }
                    })
                }), e.jsx(g, {
                    item: !0,
                    xs: 12,
                    children: e.jsx(d5, {
                        name: "interested",
                        labels: h,
                        control: d,
                        errors: l,
                        defaultValue: "buy",
                        row: !0,
                        label: r("customers.interestedTo") + "*",
                        color: "primary",
                        gap: "60px",
                        labelTextStyle: {
                            fontSize: "14px !important",
                            fontWeight: "400 !important",
                            mt: "5px",
                            mb: "5px",
                            color: "#525451"
                        },
                        labelStyle: {
                            fontSize: "14px !important",
                            fontWeight: "400 !important"
                        }
                    })
                })]
            }), e.jsx(M, {
                sx: {
                    mt: 2,
                    p: 0
                },
                children: e.jsx(dP, {
                    isLoading: s,
                    onClick: o,
                    disabled: p || s,
                    size: "large",
                    sx: {
                        margin: "15px 0px",
                        width: "100%"
                    },
                    variant: "contained",
                    type: "submit",
                    children: r("popup.save")
                })
            })]
        })
    })
};

function ete() {
    const {
        t: t
    } = Gn(), n = Ft(), {
        list: r,
        total: i,
        pagesCount: o,
        search: s,
        page: l,
        isLoading: d,
        setSearch: c,
        setPage: u,
        filters: p,
        selectedFilters: h,
        clearForm: m,
        isFilterApplied: f,
        isCreateFormOpen: g,
        setIsCreateFormOpen: y,
        handleClose: v,
        tableConfig: _
    } = Jee();
    return e.jsxs(e.Fragment, {
        children: [e.jsxs(a, {
            sx: tte.header,
            children: [e.jsx(rP, {
                s: 16,
                sx: tte.totalCustomers,
                children: i ? `${t("Total Customers")} : ${i}` : ""
            }), ni.can(qI.Create, $I.Customers) && e.jsxs(a, {
                sx: tte.headerButtons,
                children: [e.jsx(dP, {
                    variant: "outlined",
                    onClick: () => n("/marketplace/customers/upload-leads"),
                    children: t("customers.uploadLeads")
                }), e.jsx(dP, {
                    variant: "contained",
                    onClick: () => y(!0),
                    sx: tte.addButton,
                    children: t("customers.addNewLead")
                })]
            })]
        }), e.jsxs(Ne, {
            sx: tte.card,
            children: [e.jsx(Iee, {
                list: r,
                search: s,
                page: l,
                isLoading: d,
                setSearch: c,
                setPage: u,
                pagesCount: o,
                filters: p,
                selectedFilters: h,
                clearForm: m,
                isFilterApplied: f,
                tableConfig: _
            }), e.jsx(Xee, {
                open: g,
                handleClose: v
            })]
        })]
    })
}
const tte = {
    header: {
        display: "flex",
        justifyContent: "space-between",
        alignItems: "center",
        mb: 6,
        mx: 6
    },
    totalCustomers: {
        mb: 0
    },
    headerButtons: {
        display: "flex",
        gap: 8
    },
    addButton: {
        minWidth: "150px",
        height: "46px"
    },
    card: {
        mt: "20px",
        "& .MuiContainer-root": {
            padding: 0
        }
    }
};

function nte(e) {
    const {
        t: t
    } = Gn(), n = Ys(), r = Ft(), [a, i] = Dt.useState(!1), [o, s] = Dt.useState(!1), [l, d] = Dt.useState(!1), [c, u] = Dt.useState({
        isOpen: !1,
        type: "success",
        title: "",
        message: ""
    }), {
        data: p,
        isLoading: h,
        error: m,
        refetch: f
    } = tl({
        queryKey: [XI, e],
        queryFn: () => Y9(e),
        enabled: !!e
    }), g = nl({
        mutationFn: t => (async (e, t) => {
            try {
                const n = {};
                void 0 !== t.interested && (n.interested = t.interested), void 0 !== t.status && (n.status = +t.status), void 0 !== t.lead_owner_id && (n.lead_owner_id = +t.lead_owner_id || null), void 0 !== t.source && (n.source = +t.source), void 0 !== t.priority && (n.priority = +t.priority), void 0 !== t.lead_last_contact_at && (n.lead_last_contact_at = t.lead_last_contact_at), await uo(`/api-management/rf/leads/${e}`, n)
            } catch (m) {
                throw m
            }
        })(e, t),
        onSuccess: () => {
            n.invalidateQueries({
                queryKey: [XI, e]
            }), n.invalidateQueries({
                queryKey: [XI]
            }), i(!1), Zi.success(t("customerDetails.updateSuccess"))
        },
        onError: e => {
            const n = e?.response?.data?.message || t("customerDetails.updateError");
            Zi.error(n)
        }
    }), y = nl({
        mutationFn: t => (async (e, t) => {
            try {
                await co(`/api-management/rf/leads/${e}/convert`, {
                    role: t
                })
            } catch (m) {
                throw m
            }