            primaryButton: {
                title: i("properties.goto_communities"),
                handleClick: () => {
                    s.invalidateQueries([tF]), o("/properties-list/units")
                },
                variant: "contained"
            },
            onDialogClose: l
        }) : void 0
    };
var O4 = (e => (e.RESIDENTIAL = "residential", e.COMMERCIAL = "commercial", e))(O4 || {}),
    P4 = (e => (e[e.FIXED = 8] = "FIXED", e[e.PERCENTAGE = 9] = "PERCENTAGE", e))(P4 || {}),
    I4 = (e => (e[e.ANNUAL = 10] = "ANNUAL", e[e.RENTAL = 11] = "RENTAL", e))(I4 || {}),
    F4 = (e => (e[e.MONTHLY = 4] = "MONTHLY", e[e.QUARTERLY = 5] = "QUARTERLY", e[e.SEMI_ANNUAL = 6] = "SEMI_ANNUAL", e[e.ANNUAL = 7] = "ANNUAL", e))(F4 || {}),
    H4 = (e => (e[e.MONTHLY_PAYMENT = 16] = "MONTHLY_PAYMENT", e[e.UPFRONT_PAYMENT = 17] = "UPFRONT_PAYMENT", e))(H4 || {}),
    N4 = (e => (e[e.UPFRONT_PAYMENT = 18] = "UPFRONT_PAYMENT", e))(N4 || {}),
    R4 = (e => (e[e.YEARLY = 13] = "YEARLY", e[e.MONTHLY = 14] = "MONTHLY", e[e.DAILY = 15] = "DAILY", e))(R4 || {}),
    Y4 = (e => (e.MONTHLY = "monthly", e.QUARTERLY = "quarterly", e.SEMI_ANNUAL = "semi_annual", e.ANNUAL = "annual", e))(Y4 || {}),
    B4 = (e => (e[e.SHELL_AND_CORE = 1] = "SHELL_AND_CORE", e[e.FIT_OUT = 2] = "FIT_OUT", e[e.NOT_APPLICABLE = 3] = "NOT_APPLICABLE", e))(B4 || {}),
    z4 = (e => (e.FIXED = "fixed", e.PERCENTAGE = "percentage", e))(z4 || {}),
    U4 = (e => (e.TOTAL_FOR_ALL = "all", e.PER_UNIT = "detailed", e))(U4 || {}),
    W4 = (e => (e.TOTAL = "total", e.PER_SQM = "sqm", e))(W4 || {}),
    Z4 = (e => (e.YEARLY = "yearly", e.MONTHLY = "monthly", e.DAILY = "daily", e))(Z4 || {});
const q4 = () => ({
    [ui.LEASE_TYPE]: O4.RESIDENTIAL,
    [ui.LEASE_ID]: null,
    [ui.IS_RENEW]: !1,
    nationalityName: "",
    website: "",
    [ui.CONTRACT_DATES_STEP]: {
        [ui.RENTAL_CONTRACT_TYPE]: Z4.YEARLY,
        [ui.CONTRACT_CREATION_DATE]: new Date,
        [ui.LEASE_START_DATE]: null,
        [ui.LEASE_END_DATE]: null,
        [ui.HANDOVER_DATE]: null,
        [ui.YEARS]: 1,
        [ui.MONTHS]: 0,
        [ui.DAYS]: 1
    },
    [ui.UNIT_SELECTION_STEP]: {
        [ui.COMMUNITY]: [],
        [ui.ENTER_DETAILED_AMOUNT_FOR]: U4.PER_UNIT,
        [ui.TOTAL_ANNUAL_FOR_ALL_UNITS]: "",
        [ui.UNITS]: []
    },
    [ui.TENANT_DETAILS_STEP]: {
        [ui.TENANT_TYPE]: pi.INDIVIDUAL,
        [ui.TENANT]: [],
        [ui.INDIVIDUAL]: {
            [ui.FIRST_NAME]: "",
            [ui.LAST_NAME]: "",
            [ui.NATIONALITY]: "",
            [ui.NATIONAL_ID]: "",
            [ui.DATE_OF_BIRTH]: null,
            [ui.GENDER]: hi.MALE,
            [ui.PHONE_NUMBER]: "",
            [ui.PHONE_COUNTRY_CODE]: "SA",
            [ui.EMAIL]: ""
        },
        [ui.COMPANY]: {
            [ui.COMPANY_ID]: null,
            [ui.COMPANY_LOGO]: null,
            [ui.COMPANY_NAME_EN]: "",
            [ui.COMPANY_NAME_AR]: "",
            [ui.COMPANY_REGISTRATION_NO]: "",
            [ui.TAX_IDENTIFIER_NO]: "",
            [ui.NATIONAL_ADDRESS]: "",
            [ui.LEGAL_REPRESENTATIVE]: {
                [ui.LR_FIRST_NAME]: "",
                [ui.LR_LAST_NAME]: "",
                [ui.LR_NATIONAL_ID]: "",
                [ui.LR_NATIONALITY]: "",
                [ui.LR_DATE_OF_BIRTH]: null,
                [ui.LR_PHONE_NUMBER]: "",
                [ui.LR_PHONE_COUNTRY_CODE]: "SA",
                [ui.LR_EMAIL]: "",
                [ui.LR_AUTHORIZATION_NUMBER]: "",
                [ui.LEGAL_DOCUMENTS]: []
            }
        }
    },
    [ui.LEASE_DETAILS_STEP]: {
        [ui.LEASE_NUMBER]: null,
        [ui.AUTO_GEN_LEAS_NUM]: !1,
        [ui.DEAL_OWNER]: [],
        [ui.HAS_SECURITY_DEPOSIT]: !1,
        [ui.SECURITY_DEPOSIT]: "",
        [ui.SECURITY_DUE_DATE]: null,
        [ui.RENTAL_TRANSACTION_SCHEDULE]: F4.ANNUAL,
        [ui.HAS_LEASE_ESCALATION]: !1,
        [ui.ESCALATION_TYPE]: z4.FIXED,
        [ui.ESCALATIONS]: [],
        [ui.FEES_LIST]: [],
        [ui.HAS_TERMS_AND_CONDITIONS]: !1,
        [ui.TERMS_AND_CONDITIONS]: "",
        [ui.TRANSACTIONS_MARK_AS_PAID]: {},
        [ui.REVIEW_TRANSACTIONS]: []
    }
});
q4();
var $4 = (e => (e[e.CONTRACT_DATES = 0] = "CONTRACT_DATES", e[e.UNIT_SELECTION = 1] = "UNIT_SELECTION", e[e.TENANT_DETAILS = 2] = "TENANT_DETAILS", e[e.LEASE_DETAILS = 3] = "LEASE_DETAILS", e[e.REVIEW_LEASE = 4] = "REVIEW_LEASE", e))($4 || {});
const G4 = (e, t = !1) => [{
        title: e("leaseForm.leaseDates"),
        description: e("leaseForm.contractDescription"),
        value: 0
    }, {
        title: e("leaseForm.unitSelection"),
        description: e("leaseForm.unitDescription"),
        value: 1
    }, {
        title: e("leaseForm.tenantDetails"),
        description: e("leaseForm.tenantDescription"),
        value: 2
    }, {
        title: e("leaseForm.leaseDetails"),
        description: e("leaseForm.leaseDescription"),
        value: 3
    }, {
        title: e("leaseForm.reviewLease"),
        description: e("leaseForm.reviewDescription"),
        value: 4
    }],
    K4 = {
        [F4.MONTHLY]: "leaseForm.monthly",
        [F4.QUARTERLY]: "leaseForm.quarterly",
        [F4.ANNUAL]: "leaseForm.annual",
        [F4.SEMI_ANNUAL]: "leaseForm.semiAnnual",
        [H4.MONTHLY_PAYMENT]: "leaseForm.monthlyPayment",
        [H4.UPFRONT_PAYMENT]: "leaseForm.upfrontPayment",
        [N4.UPFRONT_PAYMENT]: "leaseForm.upfrontPayment"
    },
    Q4 = e => v1().shape({
        [ui.LEASE_TYPE]: a1(),
        [ui.IS_RENEW]: qX(),
        [ui.LEASE_ID]: a1().nullable(),
        [ui.CONTRACT_DATES_STEP]: v1().shape({
            [ui.RENTAL_CONTRACT_TYPE]: a1().required(e("leaseForm.rentalTypeRequired")),
            [ui.CONTRACT_CREATION_DATE]: WX().nullable().required(e("leaseForm.creationDateRequired")),
            [ui.LEASE_START_DATE]: WX().nullable().required(e("leaseForm.startDateRequired")).test("start-date-after-creation", e("leaseForm.startDateRequired"), function(e) {
                const t = this.from?.[1]?.value?.[ui.IS_RENEW];
                if (t) return !0;
                const n = this.parent[ui.CONTRACT_CREATION_DATE];
                if (!e || !n) return !0;
                const r = new Date(e),
                    a = new Date(n);
                return r.setHours(0, 0, 0, 0), a.setHours(0, 0, 0, 0), r >= a
            }),
            [ui.LEASE_END_DATE]: WX().nullable().required(e("leaseForm.endDateRequired")).test("end-date-after-today", e("leaseForm.endDateShouldBeGreaterThanToday"), function(e) {
                const t = this.from?.[1]?.value?.[ui.IS_RENEW];
                if (t) return !0;
                if (!e) return !0;
                const n = new Date;
                n.setHours(0, 0, 0, 0);
                const r = new Date(e);
                return r.setHours(0, 0, 0, 0), r >= n
            }),
            [ui.HANDOVER_DATE]: WX().nullable().required(e("leaseForm.handoverDateRequired")),
            [ui.YEARS]: o1().when(ui.RENTAL_CONTRACT_TYPE, {
                is: e => e === Z4.YEARLY,
                then: () => o1().typeError(e("leaseForm.pleaseAddLeaseDuration")).integer(e("leaseForm.theMinimumLeaseYearsDurationIsOneYear")).required(e("leaseForm.pleaseAddLeaseDuration")).min(1, e("leaseForm.theMinimumLeaseYearsDurationIsOneYear")).max(100, e("leaseForm.theMinimumLeaseYearsDurationIsOneYear")).test("no-dot", e("leaseForm.theMinimumLeaseYearsDurationIsOneYear"), e => !/\.$|\.\d+/.test(e?.toString() || "")),
                otherwise: () => o1().nullable()
            }),
            [ui.MONTHS]: o1().when(ui.RENTAL_CONTRACT_TYPE, {
                is: e => e === Z4.YEARLY || e === Z4.MONTHLY,
                then: () => o1().when(ui.RENTAL_CONTRACT_TYPE, {
                    is: e => e === Z4.YEARLY,
                    then: () => o1().typeError(e("leaseForm.pleaseAddLeaseDurationMonths")).integer(e("leaseForm.theMinimumLeaseMonthsDurationIsZero")).required(e("leaseForm.pleaseAddLeaseDurationMonths")).min(0, e("leaseForm.theMinimumLeaseMonthsDurationIsZero")).max(11, e("leaseForm.theMinimumLeaseMonthsDurationIsZero")).test("no-dot", e("leaseForm.theMinimumLeaseMonthsDurationIsZero"), e => !/\.$|\.\d+/.test(e?.toString() || "")),
                    otherwise: () => o1().typeError(e("leaseForm.pleaseAddLeaseDurationMonths")).integer(e("leaseForm.theMinimumLeaseMonthsDurationIsOneAndMaximumIsEleven")).required(e("leaseForm.pleaseAddLeaseDurationMonths")).min(1, e("leaseForm.theMinimumLeaseMonthsDurationIsOneAndMaximumIsEleven")).max(11, e("leaseForm.theMinimumLeaseMonthsDurationIsOneAndMaximumIsEleven")).test("no-dot", e("leaseForm.theMinimumLeaseMonthsDurationIsOneAndMaximumIsEleven"), e => !/\.$|\.\d+/.test(e?.toString() || ""))
                }),
                otherwise: () => o1().nullable()
            }),
            [ui.DAYS]: o1().when(ui.RENTAL_CONTRACT_TYPE, {
                is: e => e === Z4.DAILY,
                then: () => o1().typeError(e("leaseForm.pleaseAddLeaseDurationDays")).integer(e("leaseForm.theMinimumLeaseDaysDurationIsOneAndMaximumIsThirty")).required(e("leaseForm.pleaseAddLeaseDurationDays")).min(1, e("leaseForm.theMinimumLeaseDaysDurationIsOneAndMaximumIsThirty")).max(30, e("leaseForm.theMinimumLeaseDaysDurationIsOneAndMaximumIsThirty")).test("no-dot", e("leaseForm.theMinimumLeaseDaysDurationIsOneAndMaximumIsThirty"), e => !/\.$|\.\d+/.test(e?.toString() || "")),
                otherwise: () => o1().nullable()
            })
        }),
        [ui.UNIT_SELECTION_STEP]: v1().shape({
            [ui.COMMUNITY]: x1().required(e("leaseForm.pleaseEnterCommunity")).min(1, e("leaseForm.pleaseEnterCommunity")),
            [ui.ENTER_DETAILED_AMOUNT_FOR]: a1(),
            [ui.TOTAL_ANNUAL_FOR_ALL_UNITS]: a1().when(ui.ENTER_DETAILED_AMOUNT_FOR, {
                is: e => e && e == U4.TOTAL_FOR_ALL,
                then: () => a1().required(e("leaseForm.totalRentalAmountRequired")).test("is-decimal", e("leaseForm.pleaseEnterAnnualPerSqmRateWithTwoDecimalPlaces"), e => void 0 === e || /^\d+(\.\d{1,2})?$/.test(e.toString())),
                otherwise: () => a1().nullable()
            }),
            [ui.UNITS]: x1().when(ui.COMMUNITY, {
                is: e => e && e.length >= 1,
                then: () => x1().of(v1().shape({
                    [ui.CAN_ADD_RENTAL_DETAILS]: qX().default(!1),
                    [ui.RENTAL_PAYMENT_TYPE_UNIT]: a1().nullable().default(W4.TOTAL),
                    [ui.ENTER_DETAILED_AMOUNT_FOR]: a1().nullable().default(U4.PER_UNIT),
                    [ui.RENTAL_AMOUNT]: a1().nullable().when([ui.CAN_ADD_RENTAL_DETAILS, ui.ENTER_DETAILED_AMOUNT_FOR, ui.RENTAL_PAYMENT_TYPE_UNIT], {
                        is: (e, t, n) => e && n == W4.TOTAL && t == U4.PER_UNIT,
                        then: () => a1().required(e("leaseForm.rentalAmountRequired")).test("is-decimal", e("leaseForm.pleaseEnterAnnualPerSqmRateWithTwoDecimalPlaces"), e => void 0 === e || /^\d+(\.\d{1,2})?$/.test(e.toString())),
                        otherwise: () => a1().nullable()
                    }),
                    [ui.METER_COST]: a1().nullable().when([ui.CAN_ADD_RENTAL_DETAILS, ui.ENTER_DETAILED_AMOUNT_FOR, ui.RENTAL_PAYMENT_TYPE_UNIT], {
                        is: (e, t, n) => e && n == W4.PER_SQM && t == U4.PER_UNIT,
                        then: () => a1().required(e("leaseForm.rentalPerSqmRateIsRequired")).test("is-decimal", e("leaseForm.pleaseEnterAnnualPerSqmRateWithTwoDecimalPlaces"), e => void 0 === e || /^\d+(\.\d{1,2})?$/.test(e.toString())),
                        otherwise: () => a1().nullable()
                    })
                }).noUnknown(!1)).required(e("leaseForm.pleaseEnterUnit")).min(1, e("leaseForm.pleaseEnterUnit")),
                otherwise: () => x1()
            })
        }),
        [ui.TENANT_DETAILS_STEP]: v1().shape({
            [ui.TENANT_TYPE]: a1(),
            [ui.TENANT]: x1().required(e("leaseForm.pleaseEnterTenant")).min(1, e("leaseForm.pleaseEnterTenant")),
            [ui.INDIVIDUAL]: v1().shape({
                [ui.TENANT_TYPE]: a1(),
                [ui.FIRST_NAME]: a1(),
                [ui.LAST_NAME]: a1(),
                [ui.NATIONALITY]: a1().nullable(),
                [ui.NATIONAL_ID]: a1().when([ui.TENANT_TYPE, ui.TENANT], {
                    is: (e, t) => e && e === pi.INDIVIDUAL && t && t.length >= 1,
                    then: () => a1().required(e("leaseForm.nationalIdRequired")).max(15, e("leaseForm.nationalIdLength")).matches(/^[A-Za-z0-9]+$/, e("leaseForm.nationalIdSpecialChars")).matches(/^\d*[A-Za-z]*$/, e("leaseForm.nationalIdSpecialChars")),
                    otherwise: () => a1().nullable()
                }),
                [ui.DATE_OF_BIRTH]: a1().nullable(),
                [ui.GENDER]: a1().nullable(),
                [ui.PHONE_NUMBER]: a1().nullable(),
                [ui.PHONE_COUNTRY_CODE]: a1().nullable(),
                [ui.EMAIL]: a1().email(e("leaseForm.emailInvalid")).nullable()
            }),
            [ui.COMPANY]: v1().shape({
                [ui.TENANT_TYPE]: a1(),
                [ui.TENANT]: x1(),
                [ui.COMPANY_LOGO]: WX().nullable(),
                [ui.COMPANY_NAME_EN]: a1().optional(),
                [ui.COMPANY_NAME_AR]: a1(),
                [ui.COMPANY_REGISTRATION_NO]: a1().when([ui.TENANT_TYPE, ui.TENANT], {
                    is: (e, t) => e && e === pi.COMPANY && t && t.length >= 1,
                    then: () => a1().test("is-valid-pattern", e("leasing.reg_no_match"), e => !e || /^[0-9]+$/.test(e)).test("is-valid-length", e("leasing.reg_no_length"), e => !e || 10 === e.length),
                    otherwise: () => a1()
                }),
                [ui.TAX_IDENTIFIER_NO]: a1().when([ui.TENANT_TYPE, ui.TENANT], {
                    is: (e, t) => e && e === pi.COMPANY && t && t.length >= 1,
                    then: () => a1().test("is-valid-length", e("leasing.tax_match"), e => !e || /^[0-9]+$/.test(e)).test("is-valid-length", e("leasing.tax_length"), e => !e || 15 === e.length),
                    otherwise: () => a1()
                }),
                [ui.NATIONAL_ADDRESS]: a1().when([ui.TENANT_TYPE, ui.TENANT], {
                    is: (e, t) => e && e === pi.COMPANY && t && t.length >= 1,
                    then: () => a1().max(60, e("leaseForm.nationalMax60")).matches(/^(?!\s).*$/, e("leaseForm.cannotStartWithSpaces")),
                    otherwise: () => a1()
                }),
                [ui.LEGAL_REPRESENTATIVE]: v1().shape({
                    [ui.TENANT_TYPE]: a1(),
                    [ui.TENANT]: x1(),
                    [ui.LR_FIRST_NAME]: a1().when([ui.TENANT_TYPE, ui.TENANT], {
                        is: (e, t) => e && e === pi.COMPANY && t && t.length >= 1,
                        then: () => a1().required(e("leaseForm.firstNameRequired")).max(20, e("leaseForm.firstNameLength")).matches(/^[\u0621-\u064A\u0660-\u0669A-Za-z0-9\s]+$/, e("leaseForm.firstNameSpecialChars")).matches(/^(?!\s).*$/, e("leaseForm.cannotStartWithSpaces")),
                        otherwise: () => a1().nullable()
                    }),
                    [ui.LR_LAST_NAME]: a1().when([ui.TENANT_TYPE, ui.TENANT], {
                        is: (e, t) => e && e === pi.COMPANY && t && t.length >= 1,
                        then: () => a1().required(e("leaseForm.lastNameRequired")).max(20, e("leaseForm.lastNameLength")).matches(/^[\u0621-\u064A\u0660-\u0669A-Za-z0-9\s]+$/, e("leaseForm.firstNameSpecialChars")).matches(/^(?!\s).*$/, e("leaseForm.cannotStartWithSpaces")),
                        otherwise: () => a1().nullable()
                    }),
                    [ui.LR_NATIONAL_ID]: a1().when([ui.LEASE_TYPE, ui.TENANT_TYPE, ui.TENANT], {
                        is: (e, t, n) => t && t === pi.COMPANY && n && n.length >= 1 && e === O4.RESIDENTIAL,
                        then: () => a1().required(e("leaseForm.nationalIdRequired")).max(15, e("leaseForm.nationalIdLength")).matches(/^[A-Za-z0-9]+$/, e("leaseForm.nationalIdSpecialChars")),
                        otherwise: () => a1().max(15, e("leaseForm.nationalIdLength")).matches(/^[A-Za-z0-9]*$/, e("leaseForm.nationalIdSpecialChars")).nullable()
                    }),
                    [ui.LR_NATIONALITY]: a1().nullable(),
                    [ui.LR_DATE_OF_BIRTH]: a1().nullable(),
                    [ui.LR_PHONE_COUNTRY_CODE]: a1(),
                    [ui.LR_PHONE_NUMBER]: a1().when([ui.TENANT_TYPE, ui.TENANT], {
                        is: (e, t) => e && e === pi.COMPANY && t && t.length >= 1,
                        then: () => a1().required(e("leaseForm.phoneRequired")).min(9, e("leaseForm.phoneInvalid")).max(11, e("leaseForm.phoneInvalid")),
                        otherwise: () => a1().nullable()
                    }),
                    [ui.LR_EMAIL]: a1().nullable().email(e("leaseForm.emailInvalid")),
                    [ui.LR_AUTHORIZATION_NUMBER]: a1().when([ui.TENANT_TYPE, ui.TENANT], {
                        is: (e, t) => e && e === pi.COMPANY && t && t.length >= 1,
                        then: () => a1().nullable().defined().notRequired().trim().max(20, e("leaseForm.authorizationNumberLength")).matches(/^(?!-)[A-Za-z0-9]?([A-Za-z0-9!@#\$%\^\&*\)\(+=_-]*)?$/, e("leaseForm.authorizationNumberSpecialChars")),
                        otherwise: () => a1().nullable()
                    }),
                    [ui.LEGAL_DOCUMENTS]: x1().when([ui.LEASE_TYPE, ui.TENANT_TYPE, ui.TENANT], {
                        is: (e, t, n) => t && t === pi.COMPANY && n && n.length >= 1 && e === O4.RESIDENTIAL,
                        then: () => x1().min(1, e("leaseForm.pleaseAddLegalDocuments")).required(e("leaseForm.pleaseAddLegalDocuments")),
                        otherwise: () => x1()
                    })
                })
            })
        }),
        [ui.LEASE_DETAILS_STEP]: v1().shape({
            [ui.AUTO_GEN_LEAS_NUM]: qX(),
            [ui.LEASE_NUMBER]: a1().when(ui.AUTO_GEN_LEAS_NUM, {
                is: !1,
                then: () => a1().required(e("leaseForm.pleaseEnterUniqueLease")).test("no-spaces", e("leaseForm.pleaseRemoveSpaces"), e => !/\s/.test(e)).max(60, e("leaseForm.maxCharacters60")),
                otherwise: () => a1().nullable()
            }),
            [ui.RENTAL_TRANSACTION_SCHEDULE]: WX().nullable(),
            [ui.HAS_LEASE_ESCALATION]: qX(),
            [ui.ESCALATION_TYPE]: a1().nullable(),
            [ui.ESCALATIONS]: x1().of(v1().shape({
                [ui.ESCALATION_TYPE]: a1().default(z4.FIXED),
                [ui.ESCALATION_VALUE]: a1().when(ui.ESCALATION_TYPE, {
                    is: e => e && e == z4.FIXED,
                    then: () => o1().default(0).typeError(e("leaseForm.pleaseEnterFixedAmountIncreaseWithTwoDecimalPlaces")).required(e("leaseForm.pleaseEnterFixedAmountIncreaseWithTwoDecimalPlaces")).min(0, e("leaseForm.pleaseEnterFixedAmountIncreaseWithTwoDecimalPlaces")).test("is-decimal", e("leaseForm.pleaseEnterFixedAmountIncreaseWithTwoDecimalPlaces"), e => void 0 === e || /^\d+(\.\d{1,2})?$/.test(e.toString())),
                    otherwise: () => o1().default(0).typeError(e("leaseForm.pleaseEnterPercentageIncrease")).required(e("leaseForm.pleaseEnterPercentageIncrease")).min(0, e("leaseForm.pleaseEnterPercentageIncrease")).max(100, e("leaseForm.pleaseEnterPercentageIncrease"))
                })
            }).noUnknown(!1)),
            [ui.HAS_SECURITY_DEPOSIT]: qX(),
            [ui.SECURITY_DEPOSIT]: a1().when(ui.HAS_SECURITY_DEPOSIT, {
                is: !0,
                then: () => o1().required(e("leaseForm.pleaseEnterSecurityDeposit")).typeError(e("leaseForm.pleaseEnterSecurityDepositWithTwoDecimalPlaces")).positive(e("leaseForm.pleaseEnterSecurityDepositWithTwoDecimalPlaces")).moreThan(0, e("leaseForm.pleaseEnterSecurityDepositWithTwoDecimalPlaces")).test("is-decimal", e("leaseForm.pleaseEnterSecurityDepositWithTwoDecimalPlaces"), e => void 0 === e || /^\d+(\.\d{1,2})?$/.test(e.toString())),
                otherwise: () => a1()
            }),
            [ui.SECURITY_DUE_DATE]: a1().when(ui.HAS_SECURITY_DEPOSIT, {
                is: !0,
                then: () => WX().nullable().required(e("leaseForm.pleaseEnterSecurityDepositDueDate")),
                otherwise: () => WX().nullable()
            }),
            [ui.FEES_LIST]: x1().of(v1().shape({
                [ui.IS_SELECTED]: qX().nullable(),
                [ui.CALCULATION_BASIS]: WX().nullable(),
                [ui.ANNUAL_VALUE]: a1().when([ui.IS_SELECTED, ui.CALCULATION_BASIS], {
                    is: (e, t) => e && t == P4.FIXED,
                    then: () => a1().required(e("leaseForm.pleaseEnterFixedYearly")).test("is-decimal", e("leaseForm.pleaseEnterFixedYearlyWithTwoDecimalPlaces"), e => void 0 === e || /^\d+(\.\d{1,2})?$/.test(e.toString())).test("not-zero", e("leaseForm.pleaseEnterFixedYearlyWithTwoDecimalPlaces"), e => void 0 !== e && 0 !== parseFloat(e)),
                    otherwise: () => a1()
                }).when([ui.IS_SELECTED, ui.CALCULATION_BASIS], {
                    is: (e, t) => e && t == P4.PERCENTAGE,
                    then: () => o1().typeError(e("leaseForm.pleaseEnterPercentage")).required(e("leaseForm.pleaseEnterPercentageWithTwoDecimalPlaces")).min(1, e("leaseForm.pleaseEnterPercentage")).max(100, e("leaseForm.pleaseEnterPercentage"))
                }),
                [ui.PAYMENT_FREQUENCY]: WX().nullable()
            }).noUnknown(!1)),
            [ui.HAS_TERMS_AND_CONDITIONS]: qX(),
            [ui.TERMS_AND_CONDITIONS]: a1().when(ui.HAS_TERMS_AND_CONDITIONS, {
                is: !0,
                then: () => a1().required(e("leaseForm.pleaseAddTerms")).max(1e4, e("leaseForm.maxCharacters")),
                otherwise: () => a1().nullable()
            })
        })
    });

function J4(e) {
    if (null == e) return 0;
    const t = "string" == typeof e ? parseFloat(e) : e;
    return Number.isNaN(t) ? 0 : t
}

function X4(e) {
    if (null != e) return "object" == typeof e && null !== e && "Name" in e ? e.Name : String(e)
}
const e6 = e => {
        const t = e => {
            if ("" === e || null == e) return null;
            const t = Number(e);
            return Number.isNaN(t) ? null : t
        };
        return {
            lease_unit_type: e?.lease_unit_type === O4.RESIDENTIAL ? 2 : 3,
            created_at: e?.contractDatesStep?.contractCreationDate ? tR(e?.contractDatesStep?.contractCreationDate).format("YYYY-MM-DD") : null,
            start_date: e?.contractDatesStep?.leaseStartDate ? tR(e?.contractDatesStep?.leaseStartDate).format("YYYY-MM-DD") : null,
            end_date: e?.contractDatesStep?.leaseEndDate ? tR(e?.contractDatesStep?.leaseEndDate).format("YYYY-MM-DD") : null,
            handover_date: e?.contractDatesStep?.handoverDate ? tR(e?.contractDatesStep?.handoverDate).format("YYYY-MM-DD") : null,
            number_of_years: e?.contractDatesStep?.years,
            number_of_months: e?.contractDatesStep?.months,
            number_of_days: e?.contractDatesStep?.rentalContractType === Z4.YEARLY ? null : e?.contractDatesStep?.days ?? null,
            rental_contract_type_id: e?.contractDatesStep?.rentalContractType === Z4.MONTHLY ? R4.MONTHLY : e?.contractDatesStep?.rentalContractType === Z4.DAILY ? R4.DAILY : R4.YEARLY,
            units: e?.unitSelectionStep?.units?.map(e => ({
                id: e?.id,
                amount_type: e?.amount_type || "",
                rental_amount: e?.rental_amount,
                net_area: e?.amount_type === W4.TOTAL ? null : t(e?.netUnitArea),
                meter_cost: e?.amount_type === W4.TOTAL ? null : t(e?.meter_cost)
            })),
            tenant_type: e?.tenantDetailsStep?.tenantType,
            tenant_id: e?.tenantDetailsStep?.tenant?.[0]?.id,
            tenant: {
                nationality: e?.tenantDetailsStep?.individual?.nationality ?? "",
                national_id: e?.tenantDetailsStep?.individual?.nationalId,
                gender: e?.tenantDetailsStep?.individual?.gender,
                date_of_birth: e?.tenantDetailsStep?.individual?.dateOfBirth ? tR(e?.tenantDetailsStep?.individual?.dateOfBirth).format("YYYY-MM-DD") : null,
                email: e?.tenantDetailsStep?.individual?.email
            },
            company_id: e?.tenantDetailsStep?.company?.company_id,
            company: {
                company_logo: e?.tenantDetailsStep?.company?.companyLogo?.[0]?.id,
                commercial_registration_no: e?.tenantDetailsStep?.company?.companyRegistrationNo,
                tax_identifier_no: e?.tenantDetailsStep?.company?.taxIdentifierNo,
                national_address: e?.tenantDetailsStep?.company?.nationalAddress,
                name_en: e?.tenantDetailsStep?.company?.companyNameEn,
                name_ar: e?.tenantDetailsStep?.company?.companyNameAr
            },
            legal_representative: {
                first_name: e?.tenantDetailsStep?.company?.legalRepresentative?.firstName,
                last_name: e?.tenantDetailsStep?.company?.legalRepresentative?.lastName,
                nationality: e?.tenantDetailsStep?.company?.legalRepresentative?.nationality,
                national_id: e?.tenantDetailsStep?.company?.legalRepresentative?.nationalId,
                authorization_number: e?.tenantDetailsStep?.company?.legalRepresentative?.authorizationNumber,
                date_of_birth: e?.tenantDetailsStep?.company?.legalRepresentative?.dateOfBirth ? tR(e?.tenantDetailsStep?.company?.legalRepresentative?.dateOfBirth).format("YYYY-MM-DD") : null,
                country_code: e?.tenantDetailsStep?.company?.legalRepresentative?.phoneCountryCode,
                phone_number: e?.tenantDetailsStep?.company?.legalRepresentative?.phoneNumber,
                email: e?.tenantDetailsStep?.company?.legalRepresentative?.email,
                legal_documents: e?.tenantDetailsStep?.company?.legalRepresentative?.documents?.map(e => e?.id)
            },
            autoGenerateLeaseNumber: e?.leaseDetailsStep?.autoGenerateLeaseNumber,
            contract_number: e?.leaseDetailsStep?.leaseNumber,
            deal_owner_id: e?.leaseDetailsStep?.dealOwner?.[0]?.id,
            rental_type: e?.unitSelectionStep?.enterDetailedAmountFor,
            rental_total_amount: e?.unitSelectionStep?.rental_total_amount,
            rf_lease_id: e?.rf_lease_id,
            fit_out_status_id: e?.leaseDetailsStep?.fitOutStatus,
            payment_schedule_id: String(e?.leaseDetailsStep?.rentalTransactionSchedule),
            transactions: (e?.leaseDetailsStep?.reviewTransactions ?? []).map(t => {
                const n = t?.due_date ?? t?.due_on,
                    r = n && tR(n, ["YYYY-MM-DD", "DD-MM-YYYY"], !0).isValid() ? tR(n, ["YYYY-MM-DD", "DD-MM-YYYY"]).format("YYYY-MM-DD") : n;
                return {
                    id: t?.id,
                    is_paid: e?.leaseDetailsStep?.transactionsMarkAsPaid?.[String(t?.id)] ? 1 : 0,
                    due_date: r ?? ""
                }
            }),
            security_deposit_amount: e?.leaseDetailsStep?.securityDeposit,
            security_deposit_due_date: e?.leaseDetailsStep?.securityDueDate ? tR(e?.leaseDetailsStep?.securityDueDate).format("YYYY-MM-DD") : null,
            is_terms: e?.leaseDetailsStep?.termsAndConditions ? 1 : 0,
            terms_conditions: e?.leaseDetailsStep?.termsAndConditions,
            additional_fees: e?.leaseDetailsStep?.feesList?.filter(e => e?.isSelected)?.map(e => ({
                rf_additional_fees_id: e?.id,
                calculation_basis_id: e?.calculationBasis,
                payment_frequency_id: e?.paymentFrequency,
                value: e?.annualValue
            })),
            lease_escalations_type: String(e?.leaseDetailsStep?.escalationType),
            lease_escalations: e?.leaseDetailsStep?.escalations?.map(e => ({
                ...e?.escalationType !== z4.FIXED && e?.escalationType ? {
                    percentage: e?.escalationValue
                } : {
                    amount: e?.escalationValue
                }
            })) || []
        }
    },
    t6 = async e => {
        try {
            return (e => {
                const t = e?.data?.tenant_type === m$.INDIVIDUAL ? {
                    type: "individual",
                    isMoveOut: e?.data?.is_move_out,
                    name: e?.data?.tenant?.name ?? "",
                    nationalId: e?.data?.tenant?.national_id ?? "",
                    phone: e?.data?.tenant?.phone_number ?? "",
                    email: e?.data?.tenant?.email ?? "",
                    birthDate: e?.data?.tenant?.birthdate,
                    nationality: X4(e?.data?.tenant?.nationality),
                    gender: e?.data?.tenant?.gender ?? void 0,
                    photo: e?.data?.tenant?.image ? {
                        id: e?.data?.tenant?.image,
                        url: e?.data?.tenant?.image
                    } : null
                } : {
                    type: "company",
                    isMoveOut: e?.data?.is_move_out,
                    name_en: e?.data?.tenant?.company?.name_en ?? "",
                    name_ar: e?.data?.tenant?.company?.name_ar ?? "",
                    registrationNumber: e?.data?.tenant?.company?.commercial_registration_no ?? "",
                    nationalAddress: e?.data?.tenant?.company?.address,
                    taxNumber: e?.data?.tenant?.company?.tax_identifier_no ?? "",
                    website: e?.data?.tenant?.company?.website,
                    photo: e?.data?.tenant?.company?.company_logo,
                    representative: {
                        name: `${e?.data?.legal_representative?.first_name??"---"} ${e?.data?.legal_representative?.last_name??""}`,
                        nationalId: e?.data?.legal_representative?.national_id ?? "",
                        phone: e?.data?.legal_representative?.phone_number ?? "",
                        authorizationNo: e?.data?.legal_representative?.authorization_number ?? "",
                        birthDate: e?.data?.legal_representative?.date_of_birth,
                        nationality: e?.data?.legal_representative?.nationality?.Name,
                        documents: e?.data?.legal_representative?.documents,
                        email: e?.data?.legal_representative?.email
                    }
                };
                return {
                    unpaid_transactions_count: null != e?.data?.unpaid_transactions_count ? String(e.data.unpaid_transactions_count) : void 0,
                    total_unpaid_amount: null != e?.data?.total_unpaid_amount ? String(e.data.total_unpaid_amount) : void 0,
                    contract_number: e?.data?.contract_number,
                    id: e?.data?.id ?? 0,
                    isOld: "1" === e?.data?.is_old,
                    status: e?.data?.status?.id ?? 0,
                    statusName: e?.data?.status?.name ?? "",
                    pdf_url: e?.data?.pdf_url ?? "",
                    isRenew: !!e?.data?.is_renew,
                    tenant: t,
                    units: (e?.data?.units ?? []).map(e => ({
                        id: e?.unit?.id,
                        name: e?.unit?.name,
                        type: e?.unit?.type?.name,
                        category: e?.unit?.category?.name,
                        city: e?.unit?.city?.name,
                        community: e?.unit?.rf_community?.name,
                        district: e?.unit?.district?.name,
                        building: e?.unit?.rf_building?.name ?? void 0,
                        yearBuilt: e?.unit?.year_build,
                        area: parseFloat(e?.unit?.net_area),
                        rooms: e?.unit?.rooms,
                        areas: e?.unit?.areas,
                        MarketRent: isNaN(e?.unit?.market_rent?.amount_before_tax) ? null : e?.unit?.market_rent?.amount_before_tax
                    })),
                    contract: {
                        type: e?.data?.units?.[0]?.unit?.category?.name,
                        number: e?.data?.contract_number,
                        creationDate: e?.data?.created_at,
                        handoverDate: e?.data?.handover_date,
                        startDate: e?.data?.start_date,
                        endDate: e?.data?.end_date,
                        rentalSchedule: e?.data?.payment_schedule?.name,
                        rentalTypeValue: e?.data?.rental_contract_type?.id === R4.YEARLY ? Z4.YEARLY : e?.data?.rental_contract_type?.id === R4.MONTHLY ? Z4.MONTHLY : e?.data?.rental_contract_type?.id === R4.DAILY ? Z4.DAILY : void 0,
                        rentIncrease: !!(e?.data?.lease_escalations && e.data.lease_escalations.length > 0),
                        fitOutStatus: e?.data?.fit_out_status?.name,
                        owner: e?.data?.deal_owner?.name,
                        terminatedDate: e?.data?.actual_end_at ?? void 0,
                        moveOutDate: e?.data?.actual_end_at ?? void 0,
                        freePeriod: e?.data?.free_period
                    },
                    escalation: (e?.data?.lease_escalations ?? []).map((e, t) => ({
                        year: t + 2,
                        amountBeforeIncrease: e?.amount_before_increase,
                        amountAfterIncrease: e?.amount_after_increase,
                        increaseAmount: e?.increase_amount,
                        type: e?.type,
                        start_date: e?.start_date,
                        end_date: e?.end_date
                    })),
                    payment: (e?.data?.transactions ?? []).map(e => {
                        const t = "paid" === e?.type?.name || null != e?.left && 0 === Number(e.left);
                        return {
                            total: J4(e?.amount),
                            tax: J4(e?.tax_amount),
                            rent: J4(e?.rental_amount),
                            id: e?.id,
                            date: e?.due_on,
                            additionalFees: J4(e?.additional_fees_amount),
                            isPaid: t
                        }
                    }),
                    deposit: null != e?.data?.security_deposit_amount && "" !== e?.data?.security_deposit_amount ? {
                        amount: parseFloat(String(e.data.security_deposit_amount)),
                        date: e?.data?.security_deposit_due_date ?? ""
                    } : null,
                    tsAndCs: e?.data?.terms_conditions ?? "",
                    freePeriod: e?.data?.free_period ?? 0,
                    subleases: (e?.data?.subleases ?? []).map((e, t) => ({
                        id: t + 1,
                        mode: "view",
                        data: {
                            id: e?.id,
                            type: e?.tenant_type,
                            company: {
                                name_en: e?.company?.name_en,
                                name_ar: e?.company?.name_ar,
                                registrationNumber: e?.company?.registration_no,
                                taxNumber: e?.company?.tax_identifier_no,
                                note: e?.note
                            },
                            tenant: {
                                first_name: e?.tenant?.first_name,
                                last_name: e?.tenant?.last_name,
                                name: `${e?.tenant?.first_name} ${e?.tenant?.last_name}`,
                                phone: e?.tenant?.phone_number,
                                phone_country_code: e?.tenant?.phone_country_code,
                                note: e?.note,
                                nationalId: e?.tenant?.national_id
                            },
                            documents: e?.documents
                        }
                    }))
                }
            })(await lo(`/api-management/rf/leases/${e}`))
        } catch (t) {
            throw t
        }
    }, n6 = async () => {
        try {
            return await lo("/api-management/rf/leases/create")
        } catch (e) {
            throw e
        }
    }, r6 = {
        "/rf/leases/step-four": {
            code: 200,
            message: "continue",
            data: {
                lease_escalations: [{
                    id: 1,
                    start_date: "2025-10-03",
                    end_date: "2026-10-03",
                    percentage: "10",
                    amount: "20",
                    amount_before_increase: 2e3,
                    type: "fixed",
                    amount_after_increase: 2020,
                    increase_amount: "20"
                }, {
                    id: 2,
                    start_date: "2026-10-04",
                    end_date: "2027-10-05",
                    percentage: "10",
                    amount: "20",
                    amount_before_increase: 22e3,
                    type: "fixed",
                    amount_after_increase: 22020,
                    increase_amount: "20"
                }],
                transactions: [{
                    id: 1,
                    amount: 100,
                    tax: 15,
                    additional_fees: 100,
                    total_amount: 215,
                    due_date: "08-11-2023"
                }, {
                    id: 2,
                    amount: 200,
                    tax: 15,
                    additional_fees: 200,
                    total_amount: 415,
                    due_date: "08-11-2024"
                }, {
                    id: 3,
                    amount: 100,
                    tax: 15,
                    additional_fees: 100,
                    total_amount: 215,
                    due_date: "08-11-2025"
                }, {
                    id: 4,
                    amount: 200,
                    tax: 15,
                    additional_fees: 200,
                    total_amount: 415,
                    due_date: "08-11-2026"
                }],
                units: [{
                    id: 5,
                    name: "tf56",
                    category: {
                        id: 2,
                        name: "Residential"
                    },
                    type: {
                        id: 25,
                        name: "Land"
                    },
                    status: {
                        id: 26,
                        name: "Vacant",
                        created_at: null,
                        priority: "1"
                    },
                    rf_community: null,
                    rf_building: null,
                    year_build: null,
                    map: null,
                    market_rent: {
                        total_amount: "",
                        price: "",
                        amount_before_tax: ""
                    },
                    photos: [],
                    floor_plans: [],
                    documents: [],
                    net_area: null,
                    marketplace: {
                        sale: null,
                        rent: null
                    },
                    rooms: [],
                    areas: [],
                    owner: null,
                    tenant: null,
                    merge_document: [],
                    lease: [],
                    city: null,
                    district: null,
                    is_market_place: "0",
                    renewal_status: !1,
                    unit: {
                        net_area: 0
                    }
                }]
            }
        }
    }, a6 = async () => (await (async e => new Promise((t, n) => {
        setTimeout(() => {
            const r = r6[e];
            r ? t(r) : n(new Error("URL not found"))
        }, 1e3)
    }))("/rf/leases/step-four")).data, i6 = async e => {
        r6["/rf/leases/step-four"].data = e
    };
$n.t, $n.dir();
const o6 = async (e, t) => {
    try {
        const n = await fetch(e),
            r = await n.blob(),
            a = document.createElement("a");
        return a.href = URL.createObjectURL(r), a.download = t, document.body.appendChild(a), a.click(), document.body.removeChild(a), !0
    } catch (n) {
        return !1
    }
}, s6 = (e, t = "en") => {
    const n = tR.utc(e),
        r = tR.utc().diff(n),
        a = (e, t) => ({
            en: {
                seconds: "seconds",
                minutes: "minutes",
                hours: "hours",
                days: "days",
                months: "months",
                years: "years",
                ago: ""
            },
            ar: {
                seconds: "ثوانٍ",
                minutes: "دقائق",
                hours: "ساعات",
                days: "أيام",
                months: "شهور",
                years: "سنوات",
                ago: ""
            }
        } [t][e] || e),
        i = ((e, t) => {
            const n = Math.abs(e);
            return n < 6e4 ? `${Math.round(n/1e3)} ${a("seconds",t)} ${a("",t)}` : n < 36e5 ? `${Math.round(n/6e4)} ${a("minutes",t)} ${a("",t)}` : n < 864e5 ? `${Math.round(n/36e5)} ${a("hours",t)} ${a("",t)}` : n < 2592e6 ? `${Math.round(n/864e5)} ${a("days",t)} ${a("",t)}` : n < 31536e6 ? `${Math.round(n/2592e6)} ${a("months",t)} ${a("",t)}` : `${Math.round(n/31536e6)} ${a("years",t)} ${a("",t)}`
        })(r, t);
    return isNaN(+r) ? null : i
}, l6 = e => {
    const t = ["٠", "١", "٢", "٣", "٤", "٥", "٦", "٧", "٨", "٩"],
        n = e.toString().split("");
    let r = "";
    n.forEach(e => {
        /[0-9]/.test(e) ? r += t[parseInt(e)] : r += e
    });
    return r.replace(/\B(?=(\d{3})+(?!\d))/g, ",")
}, d6 = e => e?.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ","), c6 = (e, t) => e ? e.length > t ? `${e.substring(0,t)}...` : e : "---", u6 = e => {
    const t = e?.trim()?.split?.(" ");
    return t?.[0]?.charAt(0)?.toUpperCase() + (t?.length > 1 ? t?.[t?.length - 1]?.charAt(0)?.toUpperCase() : "")
}, p6 = (e, t, n) => {
    const r = new Date(e);
    let a = new Date(t) - r;
    const i = Math.floor(a / 31536e6);
    a -= 1e3 * i * 60 * 60 * 24 * 365;
    const o = Math.floor(a / 2592e6);
    a -= 1e3 * o * 60 * 60 * 24 * 30;
    const s = Math.floor(a / 864e5),
        l = [];
    return i > 0 && l.push(`${i} ${n("leaseForm.year")}`), o > 0 && l.push(`${o} ${n("leaseForm.month")}`), s > 0 && l.push(`${s} ${n("leaseForm.days")}`), l.length > 0 ? l.join(" ") : "0 " + n("leaseForm.days")
}, h6 = (e, t) => {
    const n = new Date(e);
    return new Date(t) - n
}, m6 = e => e / 315576e5, f6 = (e, t, n) => {
    if (t && n === pi.INDIVIDUAL) y6(e, `${ui.TENANT_DETAILS_STEP}.${ui.INDIVIDUAL}.${ui.FIRST_NAME}`, t?.first_name, ""), y6(e, `${ui.TENANT_DETAILS_STEP}.${ui.INDIVIDUAL}.${ui.LAST_NAME}`, t?.last_name, ""), y6(e, `${ui.TENANT_DETAILS_STEP}.${ui.INDIVIDUAL}.${ui.NATIONALITY}`, t?.nationality?.Iso2, ""), y6(e, `${ui.TENANT_DETAILS_STEP}.${ui.INDIVIDUAL}.${ui.NATIONAL_ID}`, t?.national_id, ""), y6(e, `${ui.TENANT_DETAILS_STEP}.${ui.INDIVIDUAL}.${ui.DATE_OF_BIRTH}`, t?.georgian_birthdate, null), y6(e, `${ui.TENANT_DETAILS_STEP}.${ui.INDIVIDUAL}.${ui.GENDER}`, t?.gender, null), y6(e, `${ui.TENANT_DETAILS_STEP}.${ui.INDIVIDUAL}.${ui.PHONE_NUMBER}`, t?.national_phone_number, ""), y6(e, `${ui.TENANT_DETAILS_STEP}.${ui.INDIVIDUAL}.${ui.PHONE_COUNTRY_CODE}`, t?.phone_country_code, "SA"), y6(e, `${ui.TENANT_DETAILS_STEP}.${ui.INDIVIDUAL}.${ui.EMAIL}`, t?.email, "");
    else {
        y6(e, `${ui.TENANT_DETAILS_STEP}.${ui.COMPANY}.${ui.COMPANY_LOGO}`, t?.company_logo, []), y6(e, `${ui.TENANT_DETAILS_STEP}.${ui.COMPANY}.${ui.COMPANY_ID}`, t?.id, ""), y6(e, `${ui.TENANT_DETAILS_STEP}.${ui.COMPANY}.${ui.COMPANY_NAME_EN}`, t?.name_en, ""), y6(e, `${ui.TENANT_DETAILS_STEP}.${ui.COMPANY}.${ui.COMPANY_NAME_AR}`, t?.name_ar, "");
        const n = JSON.parse(localStorage?.getItem(fi))?.tenantDetailsStep?.company,
            r = n?.companyRegistrationNo,
            a = n?.taxIdentifierNo,
            i = n?.nationalAddress;
        r ? e(`${ui.TENANT_DETAILS_STEP}.${ui.COMPANY}.${ui.COMPANY_REGISTRATION_NO}`, r) : y6(e, `${ui.TENANT_DETAILS_STEP}.${ui.COMPANY}.${ui.COMPANY_REGISTRATION_NO}`, t?.commercial_registration_no, ""), a ? e(`${ui.TENANT_DETAILS_STEP}.${ui.COMPANY}.${ui.TAX_IDENTIFIER_NO}`, a) : y6(e, `${ui.TENANT_DETAILS_STEP}.${ui.COMPANY}.${ui.TAX_IDENTIFIER_NO}`, t?.tax_identifier_no, ""), i ? e(`${ui.TENANT_DETAILS_STEP}.${ui.COMPANY}.${ui.NATIONAL_ADDRESS}`, i) : y6(e, `${ui.TENANT_DETAILS_STEP}.${ui.COMPANY}.${ui.NATIONAL_ADDRESS}`, t?.address, "");
        const o = t?.company_primary_user;
        o && (y6(e, `${ui.TENANT_DETAILS_STEP}.${ui.COMPANY}.${ui.LEGAL_REPRESENTATIVE}.${ui.LR_FIRST_NAME}`, o?.first_name, ""), y6(e, `${ui.TENANT_DETAILS_STEP}.${ui.COMPANY}.${ui.LEGAL_REPRESENTATIVE}.${ui.LR_LAST_NAME}`, o?.last_name, ""), y6(e, `${ui.TENANT_DETAILS_STEP}.${ui.COMPANY}.${ui.LEGAL_REPRESENTATIVE}.${ui.LR_NATIONAL_ID}`, o?.national_id, ""), y6(e, `${ui.TENANT_DETAILS_STEP}.${ui.COMPANY}.${ui.LEGAL_REPRESENTATIVE}.${ui.LR_NATIONALITY}`, o?.nationality?.Iso2, ""), y6(e, `${ui.TENANT_DETAILS_STEP}.${ui.COMPANY}.${ui.LEGAL_REPRESENTATIVE}.${ui.LR_DATE_OF_BIRTH}`, o?.georgian_birthdate, null), y6(e, `${ui.TENANT_DETAILS_STEP}.${ui.COMPANY}.${ui.LEGAL_REPRESENTATIVE}.${ui.LR_PHONE_NUMBER}`, o?.national_phone_number, ""), y6(e, `${ui.TENANT_DETAILS_STEP}.${ui.COMPANY}.${ui.LEGAL_REPRESENTATIVE}.${ui.LR_PHONE_COUNTRY_CODE}`, o?.phone_country_code, "SA"), y6(e, `${ui.TENANT_DETAILS_STEP}.${ui.COMPANY}.${ui.LEGAL_REPRESENTATIVE}.${ui.LR_EMAIL}`, o?.email, ""), y6(e, `${ui.TENANT_DETAILS_STEP}.${ui.COMPANY}.${ui.LEGAL_REPRESENTATIVE}.${ui.LR_AUTHORIZATION_NUMBER}`, o?.authorization_number, ""))
    }
}, g6 = (e, t) => {
    const n = tR(t?.end_date).add(1, "day").toDate();
    e(`${ui.CONTRACT_DATES_STEP}.${ui.LEASE_START_DATE}`, n), e(`${ui.CONTRACT_DATES_STEP}.${ui.HANDOVER_DATE}`, n);
    const r = t?.rental_contract_type?.id;
    let a;
    if (a = r === R4.DAILY ? Z4.DAILY : r === R4.MONTHLY ? Z4.MONTHLY : Z4.YEARLY, e(`${ui.CONTRACT_DATES_STEP}.${ui.RENTAL_CONTRACT_TYPE}`, a), a === Z4.DAILY) e(`${ui.CONTRACT_DATES_STEP}.${ui.DAYS}`, +t?.number_of_days || 1), e(`${ui.CONTRACT_DATES_STEP}.${ui.YEARS}`, null), e(`${ui.CONTRACT_DATES_STEP}.${ui.MONTHS}`, null);
    else if (a === Z4.MONTHLY) e(`${ui.CONTRACT_DATES_STEP}.${ui.MONTHS}`, +t?.number_of_months || 1), e(`${ui.CONTRACT_DATES_STEP}.${ui.YEARS}`, null), e(`${ui.CONTRACT_DATES_STEP}.${ui.DAYS}`, null);
    else {
        const {
            years: n,
            months: r
        } = ((e, t) => {
            const n = new Date(e),
                r = new Date(t);
            let a = r.getFullYear() - n.getFullYear(),
                i = r.getMonth() - n.getMonth();
            return i < 0 && (a -= 1, i += 12), {
                years: a,
                months: i
            }
        })(t?.start_date, t?.end_date);
        e(`${ui.CONTRACT_DATES_STEP}.${ui.YEARS}`, n), e(`${ui.CONTRACT_DATES_STEP}.${ui.MONTHS}`, r), e(`${ui.CONTRACT_DATES_STEP}.${ui.DAYS}`, null)
    }
    e(`${ui.UNIT_SELECTION_STEP}.${ui.COMMUNITY}`, [t?.units?.[0]?.unit?.rf_community]), e(`${ui.UNIT_SELECTION_STEP}.${ui.UNITS}`, t?.units?.map(e => ({
        ...e?.unit,
        rental_amount: +e?.annual_rental_amount,
        meter_cost: +e?.meter_cost,
        amount_before_tax: +e?.unit?.market_rent?.amount_before_tax,
        netUnitArea: +e?.net_area,
        amount_type: e?.rental_annual_type,
        city: e?.unit?.city?.name,
        district: e?.unit?.district?.name,
        building: e?.unit?.rf_building?.name,
        community: e?.unit?.rf_community?.name,
        type: e?.unit?.type?.name
    }))), e(`${ui.UNIT_SELECTION_STEP}.${ui.ENTER_DETAILED_AMOUNT_FOR}`, t?.rental_type), e(`${ui.UNIT_SELECTION_STEP}.${ui.TOTAL_ANNUAL_FOR_ALL_UNITS}`, t?.rental_total_amount);
    const i = t?.tenant_type === pi.INDIVIDUAL;
    e(`${ui.TENANT_DETAILS_STEP}.${ui.TENANT_TYPE}`, t?.tenant_type), e(`${ui.TENANT_DETAILS_STEP}.${ui.TENANT}`, [i ? t?.tenant : t?.company]), i || (e(`${ui.TENANT_DETAILS_STEP}.${ui.COMPANY}.${ui.LEGAL_REPRESENTATIVE}.${ui.LR_FIRST_NAME}`, t?.legal_representative?.first_name), e(`${ui.TENANT_DETAILS_STEP}.${ui.COMPANY}.${ui.LEGAL_REPRESENTATIVE}.${ui.LR_LAST_NAME}`, t?.legal_representative?.last_name), e(`${ui.TENANT_DETAILS_STEP}.${ui.COMPANY}.${ui.LEGAL_REPRESENTATIVE}.${ui.LR_NATIONAL_ID}`, t?.legal_representative?.national_id), e(`${ui.TENANT_DETAILS_STEP}.${ui.COMPANY}.${ui.LEGAL_REPRESENTATIVE}.${ui.LR_NATIONALITY}`, t?.legal_representative?.nationality?.Iso2), e(`${ui.TENANT_DETAILS_STEP}.${ui.COMPANY}.${ui.LEGAL_REPRESENTATIVE}.${ui.LR_DATE_OF_BIRTH}`, t?.legal_representative?.date_of_birth), e(`${ui.TENANT_DETAILS_STEP}.${ui.COMPANY}.${ui.LEGAL_REPRESENTATIVE}.${ui.LR_PHONE_NUMBER}`, t?.legal_representative?.phone_number), e(`${ui.TENANT_DETAILS_STEP}.${ui.COMPANY}.${ui.LEGAL_REPRESENTATIVE}.${ui.LR_PHONE_COUNTRY_CODE}`, t?.legal_representative?.country_code), e(`${ui.TENANT_DETAILS_STEP}.${ui.COMPANY}.${ui.LEGAL_REPRESENTATIVE}.${ui.LR_EMAIL}`, t?.legal_representative?.email), e(`${ui.TENANT_DETAILS_STEP}.${ui.COMPANY}.${ui.LEGAL_REPRESENTATIVE}.${ui.LR_AUTHORIZATION_NUMBER}`, t?.legal_representative?.authorization_number)), e(`${ui.LEASE_DETAILS_STEP}.${ui.RENTAL_TRANSACTION_SCHEDULE}`, t?.payment_schedule?.id), e(`${ui.LEASE_DETAILS_STEP}.${ui.HAS_TERMS_AND_CONDITIONS}`, "1" === t?.is_terms), e(`${ui.LEASE_DETAILS_STEP}.${ui.TERMS_AND_CONDITIONS}`, t?.terms_conditions)
}, y6 = (e, t, n, r = "") => {
    e(t, !!n ? n : r)
}, v6 = (e, t, n) => {
    const r = JSON.parse(localStorage?.getItem(fi))?.leaseDetailsStep?.feesList;
    e(`${ui.LEASE_DETAILS_STEP}.${ui.FEES_LIST}`, r?.length ? r : n?.map((e, n) => {
        const r = t(`${ui.LEASE_DETAILS_STEP}.${ui.FEES_LIST}.${n}.${ui.IS_SELECTED}`),
            a = t(`${ui.LEASE_DETAILS_STEP}.${ui.FEES_LIST}.${n}.${ui.CALCULATION_BASIS}`),
            i = t(`${ui.LEASE_DETAILS_STEP}.${ui.FEES_LIST}.${n}.${ui.ANNUAL_VALUE}`),
            o = t(`${ui.LEASE_DETAILS_STEP}.${ui.FEES_LIST}.${n}.${ui.PAYMENT_FREQUENCY}`);
        return {
            ...e,
            [ui.IS_SELECTED]: r || !1,
            [ui.CALCULATION_BASIS]: a || P4.FIXED,
            [ui.ANNUAL_VALUE]: i || "",
            [ui.PAYMENT_FREQUENCY]: o || I4.ANNUAL
        }
    }))
}, _6 = (e, t, n = [], r) => {
    const a = r?.additional_fees_lease,
        i = r?.additional_fees_lease?.map(e => +e?.additional_fees?.id),
        o = n?.map(e => ({
            ...e,
            [ui.IS_SELECTED]: i?.includes(+e?.id) || !1,
            [ui.CALCULATION_BASIS]: a?.find(t => t?.additional_fees?.id === e?.id)?.calculation_basis?.id || P4.FIXED,
            [ui.ANNUAL_VALUE]: +a?.find(t => t?.additional_fees?.id === e?.id)?.value || "",
            [ui.PAYMENT_FREQUENCY]: a?.find(t => t?.additional_fees?.id === e?.id)?.payment_frequency?.id || I4.ANNUAL
        }));
    e(`${ui.LEASE_DETAILS_STEP}.${ui.FEES_LIST}`, o)
};

function x6({
    setStep: e
} = {
    setStep: () => null
}) {
    const {
        t: t
    } = Gn(), n = Ft(), r = Ys(), [a] = $t(), i = a.get("id"), o = !!i, s = (e, t = null) => {
        if (!e) return t;
        const n = e instanceof Date ? e : new Date(e);
        return Number.isNaN(n?.getTime?.()) ? t : n
    }, {
        data: l,
        isLoading: d,
        refetch: c
    } = tl([aH, i], async () => await (async e => {
        try {
            return await lo(`/api-management/rf/leases/${e}`)
        } catch (t) {
            throw t
        }
    })(+i), {
        useErrorBoundary: !1,
        enabled: o
    }), {
        data: u,
        isLoading: p
    } = tl([aH, "CREATE"], n6, {
        useErrorBoundary: !1,
        enabled: !o
    });
    Dt.useEffect(() => {
        g.setValue(ui.LEASE_TYPE, a.get("type") ?? "residential"), g.setValue(`${ui.TENANT_DETAILS_STEP}.company.${ui.LEGAL_REPRESENTATIVE}.${ui.LEASE_TYPE}`, a.get("type") ?? "residential"), g.setValue(ui.IS_RENEW, o), g.setValue(ui.LEASE_ID, i), o && c()
    }, [a, i]);
    const h = q4();
    let m = !i && JSON.parse(localStorage?.getItem(fi)) || h;
    const f = m?.[ui.CONTRACT_DATES_STEP] || h[ui.CONTRACT_DATES_STEP];
    m = {
        ...m,
        [ui.LEASE_TYPE]: a.get("type") ?? "residential",
        [ui.IS_RENEW]: o,
        [ui.CONTRACT_DATES_STEP]: {
            ...f,
            [ui.CONTRACT_CREATION_DATE]: s(f?.[ui.CONTRACT_CREATION_DATE], new Date),
            [ui.LEASE_START_DATE]: s(f?.[ui.LEASE_START_DATE], null),
            [ui.LEASE_END_DATE]: s(f?.[ui.LEASE_END_DATE], null),
            [ui.HANDOVER_DATE]: s(f?.[ui.HANDOVER_DATE], null)
        },
        [ui.UNIT_SELECTION_STEP]: {
            ...m[ui.UNIT_SELECTION_STEP],
            [ui.UNITS]: m[ui.UNIT_SELECTION_STEP][ui.UNITS].map(e => ({
                ...e,
                [ui.CAN_ADD_RENTAL_DETAILS]: !1
            }))
        }
    };
    const g = bf({
        defaultValues: {
            ...m
        },
        resolver: L1(Q4(t)),
        mode: "onChange"
    });
    Dt.useEffect(() => {
        o && l && g6(g.setValue, l?.data)
    }, [l, i]);
    const y = g.getValues();
    Dt.useEffect(() => {
        o || localStorage.setItem(fi, JSON.stringify(y))
    }, [y]);
    const v = () => e?.(e => {
            const t = e + 1;
            if (t === $4.LEASE_DETAILS) {
                const e = y[ui.UNIT_SELECTION_STEP]?.[ui.UNITS],
                    t = e?.map(e => ({
                        ...e,
                        [ui.CAN_ADD_RENTAL_DETAILS]: !0
                    }));
                g.setValue(`${ui.UNIT_SELECTION_STEP}.${ui.UNITS}`, t)
            }
            return t
        }),
        {
            mutate: _,
            isLoading: x
        } = nl({
            mutationFn: async e => await (async e => {
                try {
                    const t = e6(e);
                    await co("/api-management/rf/leases", t)
                } catch (t) {
                    throw t
                }
            })(e),
            useErrorBoundary: !1,
            onSuccess: () => {
                Zi.success(t("common.success"), {
                    toastId: "create-lease-success"
                }), setTimeout(() => {
                    r.invalidateQueries([aH]), r.invalidateQueries([tF]), g.reset(q4()), localStorage.removeItem(fi), n("/leasing/leases")
                }, 500)
            },
            onError: () => null
        }),
        {
            mutate: b,
            isLoading: w
        } = nl({
            useErrorBoundary: !1,
            mutationFn: async e => await (async e => {
                try {
                    const t = e6(e);
                    await co("/api-management/rf/leases/renew/store", t)
                } catch (t) {
                    throw t
                }
            })(e),
            onSuccess: () => {
                Zi.success(t("common.success"), {
                    toastId: "create-lease-success"
                }), setTimeout(() => {
                    r.invalidateQueries([aH]), r.invalidateQueries([aH, i]), r.invalidateQueries([tF]), g.reset(q4()), localStorage.removeItem(fi), n("/leasing/leases")
                }, 500)
            }
        }),
        {
            mutate: C,
            isLoading: M
        } = nl({
            useErrorBoundary: !1,
            mutationFn: async ({
                data: e,
                step: t
            }) => await (async (e, t) => {
                try {
                    const n = e6(e),
                        r = await co(`/api-management/rf/leases/step-${t}`, n);
                    return r?.data
                } catch (n) {
                    throw n
                }
            })(e, t),
            onSuccess: e => {
                i6(e), g.setValue(`${ui.LEASE_DETAILS_STEP}.${ui.REVIEW_TRANSACTIONS}`, e?.transactions ?? []), v(), k()
            }
        }),
        {
            mutate: S,
            isLoading: L
        } = nl({
            useErrorBoundary: !1,
            mutationFn: async ({
                data: e,
                step: t
            }) => await (async (e, t) => {
                try {
                    const n = e6(e),
                        r = await co(`/api-management/rf/leases/renew/step-${t}`, n);
                    return r?.data
                } catch (n) {
                    throw n
                }
            })(e, t),
            onSuccess: e => {
                i6(e), g.setValue(`${ui.LEASE_DETAILS_STEP}.${ui.REVIEW_TRANSACTIONS}`, e?.transactions ?? []), v(), k()
            }
        }),