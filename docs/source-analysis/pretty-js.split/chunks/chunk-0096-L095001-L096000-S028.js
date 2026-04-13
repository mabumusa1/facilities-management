            lease_id: t.lease_id,
            tenant_type: m$.COMPANY,
            note: e.note,
            registration_no: parseInt(t.registrationNumber),
            tax_identifier_no: parseInt(t.taxNumber),
            documents: t.documents?.map(e => +e.id)
        }
    }
    const t = e;
    return {
        first_name: t.first_name,
        last_name: t.last_name,
        lease_id: t.lease_id,
        tenant_type: m$.INDIVIDUAL,
        note: t.note,
        phone_number: t.phone,
        phone_country_code: t.phone_country_code,
        documents: t.documents?.map(e => +e.id),
        national_id: t.nationalId
    }
};

function D8(e) {
    const {
        t: t
    } = Gn(), {
        id: n
    } = qt(), [r, a] = Dt.useState(e), i = Ys(), {
        mutate: o,
        isLoading: s
    } = nl({
        mutationFn: e => (async e => {
            try {
                await po(`/api-management/rf/sub-leases/${e}`)
            } catch (h) {
                throw h
            }
        })(e),
        onSuccess: () => {
            Zi.success(t("leasing.subleaseRemoved"), {
                toastId: "removeSubleaseSuccess"
            }), i.invalidateQueries([rH, n])
        },
        onError: () => {
            Zi.error(t("leasing.subleaseRemoveFailure"), {
                toastId: "removeSubleaseError"
            })
        }
    }), {
        mutate: l,
        isLoading: d
    } = nl({
        mutationFn: e => (async e => {
            try {
                const t = E8(e);
                await co("/api-management/rf/sub-leases", t)
            } catch (h) {
                throw h
            }
        })(e),
        onSuccess: () => {
            Zi.success(t("leasing.subleaseCreated"), {
                toastId: "createSubleaseSuccess"
            }), a(e => {
                const t = e[e.length - 1];
                return t && "edit" === t.mode ? e.slice(0, -1) : e
            }), i.invalidateQueries([rH, n])
        },
        onError: () => {
            Zi.error(t("leasing.subleaseCreateFailure"), {
                toastId: "createSubleaseError"
            })
        }
    }), {
        mutate: c,
        isLoading: u,
        isError: p,
        error: h
    } = nl({
        mutationFn: e => (async (e, t) => {
            try {
                const n = E8(t);
                await uo(`/api-management/rf/sub-leases/${e}`, n)
            } catch (h) {
                throw h
            }
        })(e.id, e.data),
        onSuccess: () => {
            Zi.success(t("leasing.subleaseUpdated"), {
                toastId: "updateSubleaseSuccess"
            }), a(e => e.map(e => "edit" === e.mode ? {
                ...e,
                mode: "view"
            } : e)), i.invalidateQueries([rH, n])
        },
        onError: () => {
            Zi.error(t("leasing.subleaseUpdateFailure"), {
                toastId: "updateSubleaseError"
            })
        }
    });
    if (p) {
        const e = h?.response?.data?.errors;
        Object.keys(e ?? {})?.length ? Object.values(e).forEach(e => {
            Zi.error(e[0] ?? "An error occurred while editing or creating sublease", {
                toastId: e[0]
            })
        }) : Zi.error(h?.response?.data?.message, {
            toastId: "error-creating-tenant"
        })
    }
    Dt.useEffect(() => {
        a(e)
    }, [e]);
    const m = 1 === r?.length && r?.some(e => "edit" === e.mode),
        f = Dt.useCallback(({
            subleaseDataId: e,
            subleaseId: t
        }) => {
            e ? o(e) : a(e => e.filter(e => e.id !== t))
        }, [r]),
        g = Dt.useCallback(e => {
            a(r.map(t => t.id === e ? {
                ...t,
                mode: "edit"
            } : t))
        }, [r]),
        y = Dt.useCallback((e, t) => {
            const a = r?.some(t => t?.id === e && t?.data?.id),
                i = r?.find(t => t?.id === e)?.data?.id;
            t.lease_id = +n, a ? c({
                id: i,
                data: t
            }) : l(t)
        }, [r]);
    return {
        subleases: r,
        addNewSublease: () => {
            r?.length >= 10 ? Zi.error(t("leasing.subleaseLimitReached"), {
                toastId: "subleaseLimitReached"
            }) : a([...r, {
                id: r.length + 1,
                mode: "edit",
                data: {
                    type: "company",
                    company: {
                        name_en: "",
                        name_ar: "",
                        registrationNumber: "",
                        taxNumber: "",
                        note: ""
                    },
                    tenant: {
                        first_name: "",
                        last_name: "",
                        phone: "",
                        phone_country_code: "SA",
                        nationalId: "",
                        note: ""
                    },
                    documents: []
                }
            }])
        },
        removeSublease: f,
        isRemovingSublease: s,
        editSublease: g,
        removeAllSubleases: () => {
            a([])
        },
        saveSublease: y,
        mutateLoading: d || u,
        shouldShowRemoveBtn: m
    }
}
const V8 = e => v1().shape({
        type: a1().required(e("leasing.type_required")),
        first_name: a1().required(e("leasing.fName_required")).max(50, e("leasing.fName_max")).matches(/^[\u0600-\u06FFa-zA-Z0-9 ]*$/, e("leasing.fName_match")),
        last_name: a1().required(e("leasing.lName_required")).max(50, e("leasing.lName_max")).matches(/^[\u0600-\u06FFa-zA-Z0-9 ]*$/, e("leasing.lName_match")),
        phone: a1().required(e("leasing.phone_required")).matches(/^[0-9]+$/, e("leasing.phone_match")),
        phone_country_code: a1().default("SA"),
        nationalId: a1().nullable().notRequired().max(15, e("leasing.nationalId_max")).matches(/^[a-zA-Z0-9]*$/, e("leasing.nationalId_match")),
        note: a1().max(1e4, e("leasing.note_max")).nullable().notRequired(),
        documents: x1().of(v1()).nullable().notRequired()
    }),
    A8 = e => v1().shape({
        type: a1().required(e("leasing.type_required")),
        name_en: a1().required(e("leasing.name_en_required")).max(50, e("entityCannotExceedLength", {
            entityName: e("companyName"),
            maxLength: 50
        })).matches(/^[\u0600-\u06FFa-zA-Z0-9!@#$%^&*()_+=[\]{}|\\;:'",.<>/?`~ ]*$/, e("leasing.companyName_match")),
        name_ar: a1().required(e("leasing.name_ar_required")).max(50, e("entityCannotExceedLength", {
            entityName: e("companyName"),
            maxLength: 50
        })).matches(/^[\u0600-\u06FFa-zA-Z0-9!@#$%^&*()_+=[\]{}|\\;:'",.<>/?`~ ]*$/, e("leasing.companyName_match")),
        registrationNumber: a1().nullable().notRequired().test("is-valid-pattern", e("leasing.reg_no_match"), e => !e || /^[0-9]+$/.test(e)).test("is-valid-length", e("leasing.reg_no_length"), e => !e || 10 === e.length),
        taxNumber: a1().nullable().notRequired().test("is-valid-length", e("leasing.tax_match"), e => !e || /^[0-9]+$/.test(e)).test("is-valid-length", e("leasing.tax_length"), e => !e || 15 === e.length),
        note: a1().max(1e4, e("leasing.note_max")).nullable().notRequired(),
        documents: x1().of(v1())
    }),
    O8 = () => e.jsx(cP, {
        sx: {
            border: "1px solid #ddd",
            borderRadius: "8px",
            overflow: "hidden",
            width: "140px",
            height: "150px",
            mr: "16px",
            mt: "12px"
        },
        center: !0,
        children: e.jsx(d, {
            size: 40
        })
    }),
    P8 = ({
        file: t,
        isDeleting: n,
        removeImage: r,
        onFileClick: a
    }) => {
        return e.jsxs(cP, {
            center: !0,
            column: !0,
            sx: {
                border: "1px solid #ddd",
                borderRadius: "8px",
                overflow: "hidden",
                width: "140px",
                height: "150px",
                mr: "16px",
                mt: "12px",
                "&:hover": a ? {
                    cursor: "pointer"
                } : {}
            },
            onClick: a,
            children: [(i = t?.url, !i?.match(/\.(jpeg|jpg|gif|png|webp|svg)$/) && i?.match(/\.(pdf|doc|docx|xls|xlsx|ppt|pptx|txt)$/) || i?.match(/\.(heic|heif)$/) ? e.jsx(cP, {
                sx: {
                    width: "100%",
                    height: "100%",
                    backgroundColor: e => `${e.palette.primary.main}1B`,
                    display: "flex",
                    justifyContent: "center",
                    alignItems: "center"
                },
                children: e.jsx(UH, {
                    sx: {
                        width: "35%",
                        height: "35%"
                    }
                })
            }) : e.jsx(cP, {
                height: "70%",
                center: !0,
                children: e.jsx(cP, {
                    component: "img",
                    sx: {
                        width: "100%",
                        height: "100%",
                        objectFit: "contain"
                    },
                    src: t?.url,
                    alt: "requests image"
                })
            })), e.jsxs(cP, {
                sx: {
                    bgcolor: "#ddd3",
                    borderTop: "1px solid #ddd",
                    width: "100%",
                    height: "50px"
                },
                row: !0,
                children: [e.jsx(rP, {
                    s: 10,
                    sx: {
                        overflow: "hidden",
                        textOverflow: "ellipsis",
                        whiteSpace: "nowrap",
                        width: "100%",
                        textAlign: "center",
                        height: "100%",
                        py: "13%",
                        pl: "10px"
                    },
                    light: !0,
                    children: cZ(t?.url) || "File"
                }), !n && e.jsx(dP, {
                    sx: {
                        borderRadius: "0",
                        height: "50px",
                        color: "error.main",
                        p: 0,
                        flex: "0 0 50px",
                        minWidth: "42px"
                    },
                    onClick: () => r(t?.id),
                    disabled: n,
                    children: e.jsx(th, {})
                })]
            })]
        });
        var i
    };

function I8({
    label: t,
    acceptedFiles: n,
    dropZoneArea: r,
    files: a,
    filesLimit: i,
    customErrors: o,
    maxFileSize: s,
    onFileSelect: l,
    onDelete: d,
    shouldPresetFiles: c = !0,
    acceptedFilesTypes: u,
    setIsUploading: p
}) {
    const [h, m] = Dt.useState(a), {
        t: f
    } = Gn();
    Dt.useEffect(() => {
        c && m(a)
    }, [a]);
    const {
        uploadFile: g,
        removeFile: y,
        isRemoving: v,
        isUploading: _
    } = function({
        setFiles: e,
        onDelete: t
    }) {
        const {
            t: n
        } = Gn(), [r, a] = Dt.useState(!1), [i, o] = Dt.useState(!1);
        return {
            uploadFile: async (t, r) => {
                try {
                    o(!0);
                    const n = await Vo(t),
                        a = n?.data?.data;
                    e(e => (e[r] = {
                        id: a?.id,
                        url: a?.url
                    }, [...e])), o(!1)
                } catch (ti) {
                    const a = ti?.response?.data?.errors?.name?.[0];
                    o(!1), Zi.error(a || n("common.somethingWentWrong")), e(e => [...e.filter((e, t) => t !== r)])
                }
            },
            removeFile: async n => {
                a(!0), await Ao(n), e(e => e.filter(e => "object" == typeof e && e?.id !== n)), a(!1), t?.(n)
            },
            isRemoving: r,
            isUploading: i
        }
    }({
        setFiles: m,
        onDelete: d
    }), {
        getRootProps: x,
        getInputProps: b
    } = bD({
        accept: u || {
            acceptedFiles: n
        },
        maxSize: s,
        maxFiles: i,
        onDropRejected(e) {
            e.forEach(({
                errors: e
            }) => {
                e.forEach(({
                    code: e
                }) => {
                    w(e)
                })
            })
        },
        onDrop: e => {
            if (h.length + e?.length > i) return void Zi.error(o && (o.count ?? `User can only upload ${i} files.`), {
                toastId: "too-many-files"
            });
            const t = [...h];
            m(t => [...t, ...Array.from(e, () => "")]), e.forEach((e, n) => g(e, t.length + n))
        }
    });
    Dt.useEffect(() => {
        p?.(_)
    }, [_]);
    const w = e => {
        switch (e) {
            case "file-invalid-type":
                Zi.error(o && (o.format ?? f("error.fileformat")), {
                    toastId: "file-format"
                });
                break;
            case "file-too-large":
                Zi.error(o && (o.size ?? `File size should be less than ${s} MB`), {
                    toastId: "file-too-large"
                });
                break;
            case "too-many-files":
                Zi.error(o && (o.count ?? `User can only upload ${i} files.`), {
                    toastId: "too-many-files"
                })
        }
    };
    return Dt.useEffect(() => {
        l?.(h)
    }, [h]), e.jsxs(e.Fragment, {
        children: [t && e.jsx(rP, {
            light: !0,
            s: 14,
            sx: {
                mb: "8px",
                color: "text.secondary"
            },
            children: t
        }), e.jsxs("div", {
            ...x({
                className: "dropzone"
            }),
            children: [e.jsx("input", {
                ...b()
            }), r?.() ?? e.jsx(yE, {
                maxFiles: i,
                maxFileSize: s,
                allowedFormats: n
            })]
        }), e.jsx(cP, {
            row: !0,
            children: Array.isArray(h) ? h?.map(t => "object" == typeof t ? e.jsx(P8, {
                file: t,
                removeImage: y,
                isDeleting: v
            }, t?.id) : e.jsx(O8, {})) : e.jsx(P8, {
                file: h,
                removeImage: y,
                isDeleting: v
            })
        })]
    })
}

function F8({
    form: t
}) {
    const {
        t: n,
        i18n: {
            language: r
        }
    } = Gn();
    Dt.useEffect(() => {
        t.getValues("phone_country_code") || t.setValue("phone_country_code", "SA")
    }, [t]);
    const a = [...dp.doc, ...dp.pdf, ...dp.image, ...dp.excel, ...dp.powerpoint];
    return e.jsxs(e.Fragment, {
        children: [e.jsxs(sP, {
            children: [e.jsx(lP, {
                md: 4,
                mb: "24px",
                children: e.jsx(o$, {
                    sx: {
                        width: "90%"
                    },
                    control: t.control,
                    errors: t.formState.errors,
                    name: "first_name",
                    label: n("leasing.tenant_fName"),
                    placeholder: n("leasing.tenant_name_placeholder")
                })
            }), e.jsx(lP, {
                md: 4,
                children: e.jsx(o$, {
                    sx: {
                        width: "90%"
                    },
                    control: t.control,
                    errors: t.formState.errors,
                    name: "last_name",
                    label: n("leasing.tenant_lName"),
                    placeholder: n("leasing.tenant_name_placeholder")
                })
            }), e.jsx(lP, {
                md: 4,
                children: e.jsx(x0, {
                    columnSizes: {
                        codeField: 4.5,
                        phoneField: 7.5
                    },
                    phoneCountryCodeName: "phone_country_code",
                    phoneNumberName: "phone",
                    form: t,
                    isDark: !1,
                    labelText: `${n("signIn.mobile")}*`
                })
            }), e.jsx(lP, {
                md: 4,
                children: e.jsx(o$, {
                    sx: {
                        width: "90%"
                    },
                    control: t.control,
                    errors: t.formState.errors,
                    name: "nationalId",
                    label: n("leasing.tenant_nationalId"),
                    placeholder: n("leasing.tenant_nationalId_placeholder")
                })
            }), e.jsx(lP, {
                md: 12,
                sx: {
                    mt: "24px"
                },
                children: e.jsx(o$, {
                    control: t.control,
                    errors: t.formState.errors,
                    name: "note",
                    rows: 5,
                    multiline: !0,
                    label: n("leasing.additionalNote")
                })
            })]
        }), e.jsx(cP, {
            sx: {
                my: "24px"
            },
            children: e.jsx(I8, {
                maxFileSize: lp,
                acceptedFiles: a,
                filesLimit: 10,
                files: t.getValues("documents") ?? [],
                dropZoneArea: () => e.jsx(yE, {
                    dropZoneText: n("properties.upload documents"),
                    allowedFormats: a,
                    maxFiles: 10,
                    maxFileSize: lp
                }),
                onFileSelect: e => ((e, n) => {
                    t.setValue(e, n)
                })("documents", e),
                onDelete: e => ((e, n) => {
                    t.setValue(e, t.getValues(e).filter(e => e.id !== n))
                })("documents", e),
                customErrors: {
                    size: n("leasing.fileSizeError"),
                    count: n("leasing.fileLimit"),
                    format: n("leasing.fileType")
                }
            })
        })]
    })
}

function H8({
    form: t
}) {
    const {
        t: n,
        i18n: {
            language: r
        }
    } = Gn(), a = [...dp.doc, ...dp.pdf, ...dp.image, ...dp.excel, ...dp.powerpoint];
    return e.jsxs(e.Fragment, {
        children: [e.jsxs(sP, {
            children: [e.jsx(lP, {
                md: 4,
                mb: "24px",
                children: e.jsx(o$, {
                    sx: {
                        width: "90%"
                    },
                    control: t.control,
                    errors: t.formState.errors,
                    name: "name_en",
                    label: n("leasing.company_name_en"),
                    placeholder: n("leasing.tenant_name_placeholder")
                })
            }), e.jsx(lP, {
                md: 4,
                mb: "24px",
                children: e.jsx(o$, {
                    sx: {
                        width: "90%"
                    },
                    control: t.control,
                    errors: t.formState.errors,
                    name: "name_ar",
                    label: n("leasing.company_name_ar"),
                    placeholder: n("leasing.tenant_name_placeholder")
                })
            }), e.jsx(lP, {
                md: 4,
                children: e.jsx(o$, {
                    sx: {
                        width: "90%"
                    },
                    control: t.control,
                    errors: t.formState.errors,
                    name: "registrationNumber",
                    label: n("leasing.company_number"),
                    placeholder: n("leasing.company_number_placeholder")
                })
            }), e.jsx(lP, {
                md: 4,
                children: e.jsx(o$, {
                    sx: {
                        width: "90%"
                    },
                    control: t.control,
                    errors: t.formState.errors,
                    name: "taxNumber",
                    label: n("leasing.taxNo"),
                    placeholder: n("leasing.taxNo_placeholder")
                })
            }), e.jsx(lP, {
                md: 12,
                sx: {
                    mt: "24px"
                },
                children: e.jsx(o$, {
                    control: t.control,
                    errors: t.formState.errors,
                    name: "note",
                    rows: 5,
                    multiline: !0,
                    label: n("leasing.additionalNote")
                })
            })]
        }), e.jsx(cP, {
            sx: {
                my: "24px"
            },
            children: e.jsx(I8, {
                maxFileSize: lp,
                acceptedFiles: a,
                filesLimit: 10,
                files: t.getValues("documents") ?? [],
                dropZoneArea: () => e.jsx(yE, {
                    dropZoneText: n("properties.upload documents"),
                    allowedFormats: a,
                    maxFiles: 10,
                    maxFileSize: lp
                }),
                onFileSelect: e => ((e, n) => {
                    t.setValue(e, n)
                })("documents", e),
                onDelete: e => ((e, n) => {
                    t.setValue(e, t.getValues(e).filter(e => e.id !== n))
                })("documents", e),
                customErrors: {
                    size: n("leasing.fileSizeError"),
                    count: n("leasing.fileLimit"),
                    format: n("leasing.fileType")
                }
            })
        })]
    })
}

function N8({
    data: t
}) {
    const {
        t: n,
        i18n: {
            language: r
        }
    } = Gn(), a = {
        individual: "individual" === t.type ? [{
            label: "signUp.tenantName",
            value: `${t?.tenant?.first_name??"---"} ${t?.tenant?.last_name??""}`
        }, {
            label: "signIn.mobile",
            value: t.tenant.phone
        }, {
            label: "leasing.tenant_nationalId",
            value: t.tenant.nationalId
        }, {
            label: "leasing.additionalNote",
            value: t.tenant.note,
            fullWidth: !0
        }] : [],
        company: "company" === t.type ? [{
            label: "leasing.company_name",
            value: "ar" === r ? t.company.name_ar : t.company.name_en
        }, {
            label: "leasing.company_number",
            value: t.company.registrationNumber
        }, {
            label: "leasing.taxNo",
            value: t.company.taxNumber
        }, {
            label: "leasing.additionalNote",
            value: t.company.note,
            fullWidth: !0
        }] : []
    };
    return e.jsxs(e.Fragment, {
        children: [e.jsx(cP, {
            sx: {
                display: "grid",
                gridTemplateColumns: "repeat(6, 1fr)",
                gridGap: "1rem"
            },
            children: a[t.type]?.map(t => e.jsxs(cP, {
                sx: {
                    gridColumn: t?.fullWidth ? "span 6" : ""
                },
                children: [e.jsx(rP, {
                    s: "12",
                    light: !0,
                    gray: !0,
                    children: n(t.label)
                }), e.jsx(rP, {
                    s: "16",
                    light: t.fullWidth,
                    children: t.value || "---"
                })]
            }, t.label))
        }), e.jsx(cP, {
            sx: {
                display: "flex",
                flexWrap: "wrap"
            },
            children: t.documents.map(t => e.jsx(P8, {
                file: t,
                removeImage: () => {},
                isDeleting: !0,
                onFileClick: () => window.open(t.url, "__blank")
            }, t.id))
        })]
    })
}

function R8({
    sublease: t,
    removeSublease: n,
    editSublease: r,
    saveSublease: a,
    mutateLoadingState: i
}) {
    const {
        t: o
    } = Gn(), [s, c] = Dt.useState("company"), [u, p] = Dt.useState(!1), h = bf({
        mode: "onChange",
        resolver: L1("individual" === s ? V8(o) : A8(o))
    });
    Dt.useEffect(() => {
        c(t.data.type), "individual" === t?.data?.type ? h.reset(t?.data?.tenant) : "company" === t?.data?.type && h.reset(t?.data?.company), h.setValue("type", t?.data?.type), h.setValue("documents", t?.data?.documents)
    }, [t.data, h]);
    const {
        handleSubmit: m,
        formState: {
            errors: f
        },
        control: g
    } = h, y = [{
        label: o("leasing.individual"),
        value: "individual"
    }, {
        label: o("leasing.company"),
        value: "company"
    }], [v, _] = Dt.useState(null);
    return e.jsxs(cP, {
        sx: {
            borderRadius: "16px",
            border: "1px solid #E3E3E3",
            padding: "16px 24px",
            backgroundColor: "#fff"
        },
        children: [e.jsxs(cP, {
            row: !0,
            xbetween: !0,
            mb: "24px",
            alignItems: "flex-start",
            children: [e.jsx(cP, {
                children: e.jsxs(rP, {
                    s: 24,
                    children: [o("leasing.sublease"), " ", t.id]
                })
            }), e.jsxs(cP, {
                children: ["view" === t.mode && e.jsxs(cP, {
                    row: !0,
                    gap: "8px",
                    ycenter: !0,
                    children: [i?.isRemovingSublease && t?.id === v ? e.jsx(cP, {
                        sx: {
                            "& span": {
                                width: "100% !important",
                                height: "100% !important"
                            },
                            width: "20px",
                            height: "20px"
                        },
                        children: e.jsx(d, {})
                    }) : e.jsx(w, {
                        onClick: () => {
                            p(!0), _(t.id)
                        },
                        disabled: i.isRemovingSublease,
                        sx: {
                            backgroundColor: e => `${e.palette.primary.main}17`,
                            p: "6px",
                            "& svg": {
                                width: "16px",
                                height: "16px"
                            }
                        },
                        children: e.jsx(l8, {})
                    }), e.jsx(w, {
                        onClick: () => r(t.id),
                        sx: {
                            backgroundColor: e => `${e.palette.primary.main}17`,
                            p: "6px",
                            "& svg": {
                                width: "16px",
                                height: "16px"
                            }
                        },
                        children: e.jsx(d8, {})
                    })]
                }), "edit" === t.mode && e.jsx(l, {
                    onClick: () => n({
                        subleaseDataId: t?.data?.id,
                        subleaseId: t.id
                    }),
                    sx: {
                        "& svg": {
                            width: "20px",
                            height: "20px"
                        }
                    },
                    children: e.jsx(l8, {})
                })]
            })]
        }), "edit" === t.mode ? e.jsxs("form", {
            onSubmit: m(e => {
                a(t.id, e)
            }),
            children: [e.jsx(cP, {
                mb: "24px",
                children: e.jsx(d5, {
                    name: "type",
                    labels: y,
                    control: g,
                    errors: f,
                    defaultValue: s,
                    row: !0,
                    label: o("leasing.tenantType"),
                    color: "primary",
                    gap: "60px",
                    labelTextStyle: {
                        fontSize: "14px !important",
                        fontWeight: "400 !important",
                        mb: "14px",
                        color: "#525451"
                    },
                    labelStyle: {
                        fontSize: "14px !important",
                        fontWeight: "400 !important"
                    },
                    onChange: e => c(e.target.value)
                })
            }), "individual" === s ? e.jsx(F8, {
                form: h
            }) : e.jsx(H8, {
                form: h
            }), e.jsx(l, {
                type: "submit",
                variant: "contained",
                disabled: i.mutateLoading,
                color: "primary",
                sx: {
                    backgroundColor: e => `${e.palette.primary.main}22`,
                    color: e => e.palette.primary.main,
                    width: "192px",
                    "&:hover": {
                        backgroundColor: e => `${e.palette.primary.main}2A`,
                        color: e => e.palette.primary.main
                    }
                },
                children: i.mutateLoading ? e.jsx(d, {}) : o("common.save")
            })]
        }) : e.jsx(N8, {
            data: t.data
        }), e.jsx(QW, {
            content: {
                title: o("leasing.deleteSublease"),
                body: o("leasing.deleteSubleaseBody"),
                errors: []
            },
            onDialogClose: () => p(!1),
            isOpen: u,
            primaryButton: {
                handleClick: () => {
                    n({
                        subleaseDataId: t?.data?.id,
                        subleaseId: t.id
                    }), p(!1)
                },
                disabled: i.isRemovingSublease,
                title: o("common.yes")
            },
            renderCloseBtn: () => e.jsx(l, {
                color: "inherit",
                onClick: () => p(!1),
                children: o("common.no")
            })
        })]
    })
}

function Y8({
    content: t,
    children: n,
    data: r,
    expanded: a,
    nestedName: i = !1,
    sx: o
}) {
    const {
        t: s
    } = Gn(), [l, d] = Dt.useState(a), c = i ? t?.category?.name : t?.category, u = i ? t?.type?.name : t?.type;
    return e.jsxs(ft, {
        sx: {
            boxShadow: "none",
            border: "1px solid #E3E3E3",
            borderRadius: "8px !important",
            ...o || {}
        },
        expanded: l,
        onChange: () => d(e => !e),
        children: [e.jsxs(gt, {
            expandIcon: e.jsx(JU, {}),
            sx: {
                p: "16px",
                backgroundColor: "#F0F0F0",
                "& .MuiAccordionSummary-content, .MuiAccordionSummary-content.Mui-expanded": {
                    alignItems: "center",
                    gap: "48px",
                    my: "0px",
                    display: "flex"
                }
            },
            children: [t && e.jsxs(cP, {
                row: !0,
                ycenter: !0,
                gap: "16px",
                children: [e.jsx(c8, {}), e.jsxs(cP, {
                    column: !0,
                    children: [e.jsx(rP, {
                        s: "18",
                        children: t?.name
                    }), e.jsx(rP, {
                        s: "12",
                        light: !0,
                        textTransform: "capitalize",
                        children: `${s("string"==typeof c?c.toLowerCase():"")??""}\n\t\t\t\t\t\t\t\t${c?",":""}\n\t\t\t\t\t\t\t\t${s("string"==typeof u?u.toLowerCase():"")??""}`
                    })]
                })]
            }), r?.map(e => Dt.createElement(L8, {
                ...e,
                key: e?.title
            }))]
        }), n]
    })
}
const B8 = ({
        content: t
    }) => {
        const {
            t: n
        } = Gn(), r = ((t, n) => ({
            general: [{
                icon: e.jsx(t8, {}),
                title: "editForm.city",
                body: "object" == typeof t?.city ? t?.city?.name : t?.city ?? "---"
            }, {
                icon: e.jsx(t8, {}),
                title: "editForm.district",
                body: "object" == typeof t?.district ? t?.district?.name : t?.district ?? "---"
            }, {
                icon: e.jsx(n8, {}),
                title: "community",
                body: "object" == typeof t?.community ? t?.community?.name : t?.community ?? "---"
            }, {
                icon: e.jsx(r8, {}),
                title: "properties.building",
                body: "object" == typeof t?.building ? t?.building?.name : t?.building ?? "---"
            }, {
                icon: e.jsx(a8, {}),
                title: "signUp.YearBuilt",
                body: t?.yearBuilt ? Fj(t?.yearBuilt).format("YYYY") : "---"
            }],
            areaBreakdown: t?.areas?.length > 0 ? t?.areas?.map(t => ({
                icon: e.jsx(o8, {}),