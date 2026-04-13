        k = () => {
            document.getElementById(mi).scrollIntoView({
                behavior: "smooth",
                block: "start"
            })
        };
    return {
        form: g,
        onSubmit: async e => {
            o ? b(e) : _(e)
        },
        nextStep: v,
        isLoading: x,
        isRenewLoading: w,
        isStepValidating: M,
        isLeaseLoading: d,
        isLeaseCreateLoading: p,
        isRenewStepValidating: L,
        lease: l,
        leaseCreateData: u,
        validateStep: e => C({
            data: g.getValues(),
            step: e
        }),
        validateRenewStep: e => S({
            data: g.getValues(),
            step: e
        })
    }
}
const b6 = ({
        step: t,
        setStep: n,
        children: r
    }) => {
        const {
            t: a
        } = Gn(), [i] = $t(), {
            form: o,
            onSubmit: s,
            isLoading: l,
            isRenewLoading: c,
            isLeaseLoading: u,
            validateStep: p,
            isStepValidating: h,
            nextStep: m,
            isRenewStepValidating: f,
            validateRenewStep: g
        } = x6({
            setStep: n
        }), y = o.watch(`${ui.IS_RENEW}`);
        return l || c || y && u ? e.jsx(cP, {
            xcenter: !0,
            ycenter: !0,
            fullHeight: !0,
            children: e.jsx(d, {})
        }) : e.jsx(km, {
            ...o,
            children: e.jsxs(cP, {
                component: "form",
                onSubmit: o.handleSubmit(s),
                ml: 5,
                mt: 10,
                column: !0,
                gap: "24px",
                children: [r, e.jsxs(cP, {
                    row: !0,
                    gap: "18px",
                    children: [!y && e.jsx(dP, {
                        type: "button",
                        variant: "outlined",
                        sx: {
                            width: "15%",
                            borderColor: "gray",
                            color: "gray"
                        },
                        onClick: () => {
                            const e = q4();
                            o.reset({
                                ...e,
                                [ui.LEASE_TYPE]: i.get("type") ?? "residential",
                                [ui.IS_RENEW]: !!i.get("id")
                            }), localStorage.removeItem(fi), n($4.CONTRACT_DATES)
                        },
                        children: a("leaseForm.clearDraft")
                    }), t !== $4.CONTRACT_DATES && e.jsx(dP, {
                        type: "button",
                        variant: "outlined",
                        sx: {
                            width: "15%"
                        },
                        onClick: () => {
                            n(e => {
                                const t = e - 1;
                                if (t === $4.UNIT_SELECTION) {
                                    const e = o.getValues()?.[ui.UNIT_SELECTION_STEP]?.[ui.UNITS],
                                        t = e?.map(e => ({
                                            ...e,
                                            [ui.CAN_ADD_RENTAL_DETAILS]: !1
                                        }));
                                    o.setValue(`${ui.UNIT_SELECTION_STEP}.${ui.UNITS}`, t)
                                }
                                return t
                            })
                        },
                        children: a("leaseForm.previous")
                    }), t !== $4.REVIEW_LEASE && e.jsx(dP, {
                        type: "button",
                        variant: "contained",
                        disabled: h || f,
                        sx: {
                            width: "15%"
                        },
                        onClick: async () => {
                            switch (t) {
                                case $4.CONTRACT_DATES:
                                    if (!(await o.trigger(ui.CONTRACT_DATES_STEP))) return;
                                    y ? g("one") : p("one");
                                    break;
                                case $4.UNIT_SELECTION:
                                    if (!(await o.trigger(ui.UNIT_SELECTION_STEP))) return;
                                    y ? m() : p("two");
                                    break;
                                case $4.TENANT_DETAILS:
                                    if (!(await o.trigger(ui.TENANT_DETAILS_STEP))) return;
                                    y ? m() : p("there");
                                    break;
                                case $4.LEASE_DETAILS:
                                    const e = await o.trigger(ui.LEASE_DETAILS_STEP),
                                        t = await o.trigger(ui.UNIT_SELECTION_STEP);
                                    if (!(e && t)) return;
                                    y ? g("four") : p("four");
                                case $4.REVIEW_LEASE:
                            }
                        },
                        children: a("common.next")
                    }), t === $4.REVIEW_LEASE && e.jsx(dP, {
                        type: "submit",
                        variant: "contained",
                        sx: {
                            width: "15%"
                        },
                        children: a(y ? "leasing.renew" : "common.save")
                    })]
                })]
            })
        })
    },
    w6 = ({
        children: t,
        sx: n = {}
    }) => e.jsx(cP, {
        fullWidth: !0,
        sx: {
            border: "1px solid #E3E3E3",
            borderRadius: "16px",
            padding: "36px",
            background: "white",
            ...n
        },
        children: t
    }),
    C6 = ({
        title: t,
        value: n,
        sx: r = {},
        tooltipId: a = null,
        tooltipContent: i = null
    }) => e.jsx(e.Fragment, {
        children: e.jsxs(cP, {
            row: !0,
            xbetween: !0,
            ycenter: !0,
            padding: "6px 10px",
            width: "fit-content",
            height: "fit-content",
            sx: {
                backgroundColor: "#EBF6F8",
                borderRadius: "8px",
                ...r
            },
            children: [e.jsxs(cP, {
                column: !0,
                xcenter: !0,
                children: [e.jsx(rP, {
                    s: 12,
                    light: !0,
                    children: t
                }), e.jsx(rP, {
                    s: 16,
                    children: n
                })]
            }), a && i && e.jsx(M6, {
                direction: "right",
                title: i,
                children: e.jsx("span", {
                    "data-tooltip-id": a,
                    style: {
                        paddingTop: "8px"
                    },
                    children: e.jsx(zH, {})
                })
            })]
        })
    }),
    M6 = ({
        title: t,
        children: n,
        direction: r = "bottom"
    }) => e.jsx(y, {
        title: t,
        arrow: !0,
        placement: r,
        sx: {
            [`& .${xt.tooltip}`]: {
                backgroundColor: "#008EA5",
                color: "#fff",
                fontSize: "12px",
                fontWeight: 400,
                maxWidth: 250,
                padding: "4px 10px",
                borderRadius: "6px"
            }
        },
        children: n
    }),
    S6 = ({
        title: t,
        subtitle: n,
        selected: r,
        onClick: a
    }) => e.jsxs(ap, {
        onClick: a,
        sx: {
            flex: 1,
            border: e => r ? `2px solid ${e.palette.primary.main}` : "1px solid #E0E0E0",
            borderRadius: "12px",
            padding: "32px",
            cursor: "pointer",
            textAlign: "center",
            backgroundColor: r ? e => u(e.palette.primary.main, .05) : "#F9F9F9",
            transition: "all 0.2s ease-in-out",
            "&:hover": {
                backgroundColor: "#F4F4F4",
                transform: "translateY(-2px)",
                boxShadow: "0 4px 12px rgba(0, 0, 0, 0.08)"
            }
        },
        children: [e.jsx(hp, {
            variant: "h6",
            sx: {
                marginBottom: "6px",
                fontSize: "20px",
                color: r ? "primary.main" : "text.primary"
            },
            children: t
        }), e.jsx(hp, {
            variant: "label",
            light: !0,
            sx: {
                color: "color.secondary"
            },
            children: n
        })]
    }),
    L6 = t => {
        const n = c();
        return e.jsx(i, {
            ...t,
            inheritViewBox: !0,
            children: e.jsxs("svg", {
                width: "35",
                height: "38",
                viewBox: "0 0 35 38",
                fill: "none",
                xmlns: "http://www.w3.org/2000/svg",
                children: [e.jsx("g", {
                    "clip-path": "url(#clip0_37892_161047)",
                    children: e.jsx("path", {
                        d: "M4.17018 4.45362L22.5014 1.67496C22.6049 1.6592 22.7103 1.66724 22.8106 1.69853C22.9109 1.72981 23.0036 1.78361 23.0826 1.85628C23.1616 1.92895 23.2249 2.01879 23.2683 2.11971C23.3117 2.22063 23.3342 2.33028 23.3341 2.44122V34.7108C23.3341 34.8216 23.3117 34.9311 23.2684 35.0319C23.2251 35.1327 23.1619 35.2225 23.0831 35.2951C23.0043 35.3677 22.9117 35.4216 22.8116 35.453C22.7115 35.4844 22.6062 35.4926 22.5029 35.4771L4.16872 32.6984C3.82108 32.6459 3.50298 32.4619 3.27285 32.1804C3.04272 31.8989 2.91602 31.5386 2.91602 31.1659V5.98614C2.91602 5.6134 3.04272 5.25318 3.27285 4.97164C3.50298 4.69011 3.82108 4.50617 4.16872 4.45362H4.17018ZM5.83414 7.3298V29.8222L20.4175 32.0343V5.11771L5.83414 7.3298ZM24.7925 29.412H29.1675V7.74002H24.7925V4.64402H30.6258C31.0126 4.64402 31.3835 4.80711 31.657 5.09742C31.9305 5.38773 32.0841 5.78147 32.0841 6.19202V30.96C32.0841 31.3706 31.9305 31.7643 31.657 32.0546C31.3835 32.3449 31.0126 32.508 30.6258 32.508H24.7925V29.412ZM14.8758 18.576L18.9591 24.768H15.4591L13.1258 21.2293L10.7925 24.768H7.29247L11.3758 18.576L7.29247 12.384H10.7925L13.1258 15.9227L15.4591 12.384H18.9591L14.8758 18.576Z",
                        fill: n?.palette?.primary?.main
                    })
                }), e.jsx("defs", {
                    children: e.jsx("clipPath", {
                        id: "clip0_37892_161047",
                        children: e.jsx("rect", {
                            width: "35",
                            height: "37.152",
                            fill: "white"
                        })
                    })
                })]
            })
        })
    },
    k6 = ({
        excelFile: t,
        isLoading: n,
        handleRemove: r
    }) => e.jsxs(ap, {
        xbetween: !0,
        ycenter: !0,
        sx: {
            border: "1px solid #E0E0E0",
            borderRadius: "12px",
            padding: "16px",
            mt: "4px"
        },
        children: [e.jsxs(ap, {
            row: !0,
            ycenter: !0,
            sx: {
                width: "100%",
                flex: 1
            },
            children: [e.jsx(L6, {
                sx: {
                    width: "35px",
                    height: "38px",
                    color: "primary.main",
                    mr: "12px"
                }
            }), e.jsxs(ap, {
                children: [e.jsx(hp, {
                    s: 14,
                    bold: !0,
                    sx: {
                        ml: 2
                    },
                    children: t[0]?.name
                }), e.jsxs(hp, {
                    s: 12,
                    light: !0,
                    sx: {
                        ml: 2,
                        textTransform: "uppercase"
                    },
                    children: ["Excel", " "]
                })]
            })]
        }), n && e.jsx(e.Fragment, {
            children: e.jsx(ap, {
                sx: {
                    width: "100%",
                    flex: 1.1
                },
                children: e.jsx(bt, {
                    sx: {
                        mx: "12px"
                    }
                })
            })
        }), e.jsx(w, {
            disabled: n,
            onClick: r,
            children: e.jsx(ph, {})
        })]
    }),
    T6 = () => {
        const t = qt(),
            n = t?.id,
            {
                t: r
            } = Gn(),
            {
                status: a,
                handleSubmit: i,
                form: o,
                watch: s,
                isLoading: l,
                handleTryAgain: d,
                excelErrors: c,
                apiErrorMessage: u,
                onSubmit: p
            } = (e => {
                const [t, n] = Dt.useState(!1), [r, a] = Dt.useState(null), [i, o] = Dt.useState(null), [s, l] = Dt.useState(null), d = bf({
                    defaultValues: {
                        propertyType: "land"
                    }
                }), {
                    register: c,
                    handleSubmit: u,
                    watch: p,
                    formState: {
                        errors: h
                    }
                } = d, m = async t => {
                    const n = new FormData,
                        r = t.file,
                        a = t.propertyType || "land";
                    n.append("file", r[0]), n.append("rf_community_id", e);
                    const i = "land" === a ? "/rf/excel-sheets/land" : "/rf/excel-sheets";
                    try {
                        await bo.post(i, n, {
                            headers: {
                                "Content-Type": "multipart/form-data"
                            }
                        })
                    } catch (o) {
                        throw o
                    }
                };
                return {
                    register: c,
                    handleSubmit: u,
                    errors: h,
                    onSubmit: async e => {
                        const t = e || d.getValues();
                        n(!0);
                        try {
                            await m(t), a("success"), n(!1)
                        } catch (r) {
                            o(r?.response?.data?.errors), l(r?.response?.data?.message), n(!1), a("failed"), Lo(r, void 0, !0)
                        }
                    },
                    form: d,
                    watch: p,
                    status: r,
                    setStatus: a,
                    handleTryAgain: () => {
                        a(!1), d.resetField("file")
                    },
                    isLoading: t,
                    apiErrorMessage: s,
                    excelErrors: i
                }
            })(n),
            h = s("file"),
            m = o.watch("propertyType");
        return e.jsxs(ap, {
            mt: "28px",
            column: !0,
            gap: "24px",
            maxWidth: "lg",
            children: [e.jsxs(w6, {
                children: [e.jsx(IQ, {}), e.jsx(hp, {
                    variant: "h4",
                    mt: "32px",
                    mb: "4px",
                    children: r("properties.bulkUploadTitle")
                }), e.jsx(hp, {
                    variant: "body",
                    children: r("bulkUploadDescription")
                })]
            }), e.jsxs(w6, {
                children: [e.jsx(hp, {
                    variant: "h5",
                    mb: "24px",
                    children: r("bulkUploadStep1")
                }), e.jsxs(ap, {
                    row: !0,
                    gap: "24px",
                    maxWidth: "sm",
                    children: [e.jsx(S6, {
                        title: r("Land Properties"),
                        subtitle: r("Commercial and Residential Lands"),
                        selected: "land" === m,
                        onClick: () => o.setValue("propertyType", "land")
                    }), e.jsx(S6, {
                        title: r("Other Properties"),
                        subtitle: r("Commercial and Residential Units"),
                        selected: "other" === m,
                        onClick: () => o.setValue("propertyType", "other")
                    })]
                })]
            }), e.jsx(w6, {
                children: e.jsxs(ap, {
                    row: !0,
                    xbetween: !0,
                    ycenter: !0,
                    children: [e.jsxs(ap, {
                        children: [e.jsx(hp, {
                            variant: "h5",
                            mb: "4px",
                            children: r("bulkUploadStep2")
                        }), e.jsx(hp, {
                            variant: "body",
                            color: "text.secondary",
                            children: r("bulkUploadStep2Description")
                        }), e.jsx(Cp, {
                            sx: {
                                mt: "12px"
                            },
                            body: r("bulkUploadStep2Note")
                        })]
                    }), e.jsx(wp, {
                        startIcon: e.jsx(WN.DownloadIcon, {}),
                        onClick: () => {
                            "land" === m ? (async () => {
                                try {
                                    const e = (await lo("/api/general/static-files/download_land_excel")).data.url;
                                    window.open(e, "_blank")
                                } catch (e) {
                                    throw e
                                }
                            })() : (async () => {
                                try {
                                    const e = (await lo("/api/general/static-files/download_unit_excel?types=download_unit_excel")).data.url;
                                    window.open(e, "_blank")
                                } catch (e) {
                                    throw e
                                }
                            })()
                        },
                        variant: "contained",
                        children: r("land" === m ? "Download Land Template" : "Download Properties Template")
                    })]
                })
            }), e.jsxs(w6, {
                children: [e.jsx(hp, {
                    variant: "h5",
                    mb: "24px",
                    children: r("bulkUploadStep3")
                }), e.jsxs("form", {
                    onSubmit: i(async () => {
                        const e = {
                            ...o.getValues(),
                            propertyType: m
                        };
                        return p(e)
                    }),
                    autoComplete: "off",
                    children: [e.jsx(E4, {
                        showThumbs: !1,
                        name: "file",
                        label: "",
                        maxFileSize: lp,
                        dropzoneText: "",
                        defaultPreview: !0,
                        dropZoneAreaComponent: () => e.jsx(yE, {
                            dropZoneText: r("Upload Completed Excel File"),
                            allowedFormats: dp.excel,
                            maxFiles: 1,
                            maxFileSize: lp
                        }),
                        dropzoneArea: !0,
                        acceptedFiles: dp.excel,
                        filesLimit: 1,
                        customErrors: {
                            format: r("error.fileformat")
                        },
                        form: o,
                        errors: o.formState.errors,
                        rules: {
                            required: !0
                        },
                        displayLogo: V4
                    }), h && e.jsx(k6, {
                        excelFile: h,
                        handleRemove: () => {
                            o.resetField("file")
                        },
                        isLoading: l
                    }), e.jsx(ap, {
                        sx: {
                            mt: 18,
                            textAlign: "left"
                        },
                        children: e.jsx(wp, {
                            isLoading: l,
                            disabled: !h || l,
                            size: "large",
                            fullWidth: !1,
                            type: "submit",
                            variant: "contained",
                            sx: {
                                px: 40
                            },
                            children: r("Upload & Save")
                        })
                    })]
                }), e.jsx(A4, {
                    status: a,
                    handleTryAgain: d,
                    excelErrors: c,
                    apiErrorMessage: u
                })]
            })]
        })
    },
    j6 = async e => await co("/api-management/rf/facilities", e), E6 = async (e, t) => await uo(`/api-management/rf/facilities/${t}`, e), D6 = async e => (await lo(`/api-management/rf/facilities/${e}`)).data, V6 = async ({
        query: e,
        page: t
    }) => (await lo(`/api-management/rf/communities?is_paginate=1&has_facility=1&search=${e}&page=${t}`)).data;

function A6({
    search: t,
    setSearch: n,
    community: r,
    fromCommunity: a
}) {
    const {
        t: i,
        i18n: {
            language: o
        }
    } = Gn(), [s] = $t(), l = Ft();
    return e.jsxs(cP, {
        component: "header",
        mb: "36px",
        children: [e.jsx(IQ, {
            color: "inherit",
            sx: {
                display: "flex",
                alignItems: "center",
                gap: 2,
                p: "10px 20px",
                borderRadius: "8px",
                border: "1px solid #CACACA",
                mb: "28px"
            },
            handleBackAction: () => {
                const e = s.get("is_settings");
                e && !["undefined", 0, !1].includes(e) ? l("/settings?tab=4") : l("/properties-list/communities")
            }
        }), e.jsxs(rP, {
            s: 36,
            mb: "24px",
            textTransform: "capitalize",
            sx: {
                direction: "ar" === o ? "rtl" : "ltr",
                textAlign: "ar" === o ? "left" : ""
            },
            children: [r?.name, " ", i("facilitiesBooking.facilities")]
        }), e.jsxs(cP, {
            sx: {
                display: "flex",
                alignItems: "center",
                justifyContent: "space-between",
                "& .MuiBox-root:has(> .MuiFormControl-root)": {
                    width: "35%",
                    height: "52px"
                },
                "& .MuiTextField-root": {
                    width: "100%",
                    height: "100%",
                    display: "block"
                },
                "& .MuiFormControl-root.MuiTextField-root .MuiInputBase-root": {
                    backgroundColor: "transparent !important",
                    height: "100%",
                    width: "100%",
                    borderWidth: "0 !important"
                },
                "& .MuiFormControl-root.MuiTextField-root .MuiInputBase-root fieldset": {
                    borderColor: "#E3E3E3 !important"
                }
            },
            children: [e.jsx(RQ, {
                isGrayIcon: !0,
                search: t,
                handleSearch: n
            }), e.jsx(dP, {
                sx: {
                    p: "12px 24px"
                },
                variant: "outlined",
                onClick: () => {
                    l(a ? `/properties-list/community/details/${r?.id}/addNewFacility/?complex_id=${r?.id}` : `/settings/addNewFacility?complex_id=${r?.id}`)
                },
                children: i("Add New Facility")
            })]
        })]
    })
}
var O6 = (e => (e.CUSTOM = "custom", e.NO_CAPACITY = "no_capacity", e.ALL_DAY = "all-day", e.ALL = "all", e.BOTH = "both", e.MALE = "male", e.FEMALE = "female", e.DAILY = "daily", e.HOURLY = "hourly", e.WEEKLY = "weekly", e.COMMUNITY = "community", e.BUILDING = "building", e.SHARED = "shared", e.PRIVATE = "private", e))(O6 || {});
const P6 = e => ({
    typeOptions: [{
        label: e("all"),
        value: O6.ALL
    }, {
        label: e("properties.community"),
        value: O6.COMMUNITY
    }, {
        label: e("properties.building"),
        value: O6.BUILDING
    }],
    capacityLimitOptions: [{
        label: e("serviceSettings.noCapacity"),
        value: O6.NO_CAPACITY
    }, {
        label: e("serviceSettings.custom"),
        value: O6.CUSTOM
    }],
    capacityTypesOptions: [{
        label: e("serviceSettings.daily"),
        value: O6.DAILY
    }, {
        label: e("serviceSettings.hourly"),
        value: O6.HOURLY
    }, {
        label: e("serviceSettings.weekly"),
        value: O6.WEEKLY
    }],
    workingHoursOptions: [{
        label: e("serviceSettings.allDay"),
        value: O6.ALL_DAY
    }, {
        label: e("serviceSettings.custom"),
        value: O6.CUSTOM
    }],
    genderOptions: [{
        label: e("all"),
        value: O6.BOTH
    }, {
        label: e("male"),
        value: O6.MALE
    }, {
        label: e("female"),
        value: O6.FEMALE
    }],
    facilityTypes: [{
        label: e("facilitiesBooking.shared"),
        value: "shared"
    }, {
        label: e("facilitiesBooking.private"),
        value: "private"
    }]
});
var I6 = (e => (e[e.HALF_HOUR = 30] = "HALF_HOUR", e[e.ONE_HOUR = 60] = "ONE_HOUR", e))(I6 || {});
const F6 = e => Boolean(e?.start_time) || Boolean(e?.end_time) ? O6.CUSTOM : O6.ALL_DAY,
    H6 = e => ({
        name_en: e?.name_en ?? "",
        name_ar: e?.name_ar ?? "",
        description: e?.description ?? "",
        start_time: e?.start_time ?? null,
        end_time: e?.end_time ?? null,
        booking_type: e?.booking_type ?? null,
        capacity: Number(e?.capacity) ?? null,
        gender: e?.gender ?? O6.BOTH,
        age: e?.age ?? null,
        reservation_duration: e?.reservation_duration ?? 0,
        days: N6(e?.days),
        working_hours_type: F6(e),
        capacity_limit: e?.capacity ? O6.CUSTOM : O6.NO_CAPACITY,
        approved: Boolean(e?.approved),
        images: e?.images && 0 !== e?.images.length ? e?.images : null,
        file: e?.images && 0 !== e?.images.length ? [{
            ...e?.images
        }] : null
    }),
    N6 = e => {
        const t = ["monday", "tuesday", "wednesday", "thursday", "friday", "saturday", "sunday"];
        return Y6(e) ? t : t.map(t => !!e?.includes(t) && t)
    },
    R6 = (e, t) => e.find(e => e.value === t)?.label,
    Y6 = e => 1 === e?.length && "All" === e?.[0],
    B6 = e => {
        const t = e?.filter(e => e).map(e => "object" == typeof e ? e.name.toLowerCase() : e.toLowerCase());
        return t
    },
    z6 = (e, t, n) => {
        let r = e === I6.ONE_HOUR ? t("serviceSettings.hour") : t("serviceSettings.hours");
        "ar" === n && e % I6.ONE_HOUR !== 0 && (r = t("serviceSettings.hour"));
        return e < I6.ONE_HOUR ? `30 ${t("serviceSettings.min")}` : `${e/I6.ONE_HOUR} ${r}`
    },
    U6 = ({
        days: e,
        t: t,
        charLength: n
    }) => Y6(e) || 7 === e?.length ? t("facilitiesBooking.allDays") : e?.map(e => bh.startCase(e).substring(0, n ?? 3)).join(","),
    W6 = ({
        days: e,
        t: t
    }) => Y6(e) || 7 === e?.length ? t("facilitiesBooking.allDays") : e?.map(e => t(`weekDays.${e?.toLowerCase()}`)).join(", "),
    Z6 = ({
        data: e,
        timeFormatter: t,
        t: n
    }) => F6(e) === O6.ALL_DAY ? n("facilitiesBooking.allDay") : `${t(e?.start_time)} - ${t(e?.end_time)}`,
    q6 = (e, t) => {
        if (e) {
            const n = e;
            let r = Number(n?.split(":")[0]);
            const a = n?.split(":")[1];
            let i;
            return r >= 12 ? (i = t("pm"), 12 != r && (r -= 12)) : (0 == r && (r = 12), i = t("am")), `${r}:${a} ${i}`
        }
        return t("N/A")
    };

function $6({
    facility: t
}) {
    const {
        t: n,
        i18n: {
            language: r
        }
    } = Gn(), a = Ft(), [i] = $t(), o = i.get("is_settings"), s = P6(n).facilityTypes, l = [{
        label: "facilitiesBooking.facilityType",
        value: R6(s, t?.booking_type)
    }, {
        label: "ageLimit",
        value: Number(t?.age) ? `${t?.age}+` : n("noLimits")
    }, {
        label: "workingDays",
        value: "ar" === r ? W6({
            days: t?.days,
            t: n
        }) : U6({
            days: t?.days,
            t: n,
            charLength: 1
        })
    }, {
        label: "facilitiesBooking.workingTime",
        value: Z6({
            data: t,
            timeFormatter: e => q6(e, n),
            t: n
        })
    }];
    return e.jsxs(Ne, {
        children: [e.jsxs(et, {
            sx: {
                p: "24px"
            },
            children: [e.jsx(cP, {
                component: "header",
                sx: {
                    mb: "24px",
                    height: "130px"
                },
                children: t?.images?.url ? e.jsx(cP, {
                    "data-testid": "facility-img",
                    component: "img",
                    src: t?.images?.url ?? "/assets/png/facility-placeholder-CLyOgskm.png",
                    sx: {
                        width: "100%",
                        height: "130px",
                        objectFit: "cover",
                        borderRadius: "16px"
                    },
                    loading: "lazy"
                }) : e.jsx(cP, {
                    sx: {
                        "& svg": {
                            width: "100%",
                            height: "100%"
                        },
                        height: "100%"
                    },
                    children: e.jsx(WH, {})
                })
            }), e.jsx(cP, {
                component: "section",
                mb: "16px",
                children: e.jsxs(cP, {
                    component: "header",
                    children: [e.jsx(rP, {
                        s: "12",
                        light: !0,
                        mb: "8px",
                        color: "#525451",
                        children: n("facilitiesBooking.facilityName")
                    }), e.jsx(rP, {
                        s: "24",
                        color: "#232425",
                        children: t?.name
                    })]
                })
            }), e.jsx(cP, {
                component: "section",
                sx: {
                    display: "flex",
                    justifyContent: "space-between",
                    gap: "8px",
                    flexWrap: "wrap"
                },
                children: l.map(({
                    label: t,
                    value: r
                }, a) => e.jsxs(cP, {
                    sx: {
                        width: 2 === a ? "calc(20%)" : "unset",
                        flex: "45%"
                    },
                    children: [e.jsx(rP, {
                        s: "12",
                        light: !0,
                        mb: "8px",
                        color: "#525451",
                        sx: {
                            textWrap: "nowrap"
                        },
                        children: n(t)
                    }), e.jsx(rP, {
                        "data-testid": t,
                        s: "16",
                        color: "#232425",
                        light: !0,
                        sx: {
                            textWrap: "nowrap",
                            overflow: "hidden",
                            textOverflow: "ellipsis"
                        },
                        children: r
                    })]
                }, t))
            })]
        }), e.jsx(wt, {
            sx: {
                borderTop: "1px solid #F0F0F0",
                p: "16px 24px"
            },
            children: e.jsx(dP, {
                sx: {
                    backgroundColor: e => `${e.palette.primary.main}14`,
                    mb: "8px",
                    py: "9px",
                    fontSize: "12px !important",
                    fontWeight: "700",
                    borderRadius: "9px",
                    "&:hover": {
                        backgroundColor: e => `${e.palette.primary.main}24`
                    }
                },
                onClick: () => a(`/settings/facility/${t?.id}?complex_id=${t?.community?.id}&community_name=${t?.community?.name}${o?`&is_settings=${o}`:""}`),
                fullWidth: !0,
                "aria-label": "View Details",
                children: n("signUp.viewDetails")
            })
        })]
    })
}

function G6(e, t) {
    const [n, r] = Dt.useState([]), [a, i] = Dt.useState([]), [o, s] = Dt.useState(!0), [l, d] = Dt.useState(0);
    let c = 20;
    const u = () => {
        (() => {
            const e = document.querySelector("main.MuiBox-root");
            return Math.abs(e.scrollHeight - e.scrollTop - e.clientHeight) < 1
        })() && !t && (c > n?.length - a?.length && (c = n?.length - a?.length), l + c <= n?.length ? (i(e => [...e, ...n.slice(l, l + c)]), d(e => e + c)) : s(!1))
    };
    return Dt.useEffect(() => (o && document.querySelector("main.MuiBox-root")?.addEventListener("scroll", u), () => document.querySelector("main.MuiBox-root")?.removeEventListener("scroll", u)), [u, o]), Dt.useEffect(() => {
        r(e), i(e?.slice(0, c)), d(e => e + c)
    }, [JSON.stringify(e)]), {
        displayedItems: a
    }
}
const K6 = ({
        fromCommunity: t = !1
    }) => {
        const {
            t: n
        } = Gn(), [r, a] = $t(), [i, o] = Dt.useState(""), {
            data: s,
            isLoading: l
        } = tl([AF, i, r.get("complex_id")], async () => await (async ({
            search: e,
            complex_id: t
        }) => (await lo(`/api-management/rf/facilities?query=${e}&community_id=${t}`)).data)({
            complex_id: r.get("complex_id") ?? void 0,
            search: i
        }));
        Dt.useEffect(() => {
            const e = r.get("community_name");
            e && "undefined" !== e || a({
                complex_id: r.get("complex_id"),
                community_name: s?.[0]?.community?.name
            })
        }, [JSON.stringify(s)]);
        const {
            displayedItems: d
        } = G6(s, l);
        return e.jsxs(cP, {
            children: [e.jsx(A6, {
                fromCommunity: t,
                search: i,
                setSearch: o,
                community: {
                    name: r.get("community_name"),
                    id: r.get("complex_id")
                }
            }), l && e.jsx(hP, {}), 0 === s?.length ? e.jsxs(cP, {
                sx: {
                    my: "16rem",
                    width: "100%",
                    mx: "auto",
                    textAlign: "center"
                },
                children: [e.jsx(cP, {
                    component: "img",
                    sx: {
                        display: "inline-block",
                        mb: "2rem",
                        p: "0.2rem",
                        filter: "grayscale(0.9)"
                    },
                    src: wI
                }), e.jsx(rP, {
                    s: "36",
                    sx: {
                        mb: "0.7rem",
                        textTransform: "capitalize"
                    },
                    children: n("common.NoDataAvailable")
                })]