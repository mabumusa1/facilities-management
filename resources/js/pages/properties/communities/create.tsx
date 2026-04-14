import { Head, Link, useForm } from "@inertiajs/react";
import { ArrowLeft, ChevronDown, UploadCloud } from "lucide-react";
import { useEffect } from "react";
import { Button } from "@/components/ui/button";
import { Card, CardContent } from "@/components/ui/card";
import { Checkbox } from "@/components/ui/checkbox";
import { Input } from "@/components/ui/input";
import { Label } from "@/components/ui/label";
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from "@/components/ui/select";
import { Textarea } from "@/components/ui/textarea";
import {
    index as communitiesIndex,
    store as communitiesStore,
} from "@/routes/communities";

interface Country {
    id: number;
    name: string;
    name_ar?: string | null;
    currency_code?: string | null;
}

interface Currency {
    id: number;
    name: string;
    name_ar?: string | null;
    code: string;
}

interface City {
    id: number;
    name: string;
    name_ar?: string | null;
    country_id: number;
}

interface District {
    id: number;
    name: string;
    name_ar?: string | null;
    city_id: number;
}

interface Amenity {
    id: number;
    name: string;
    name_ar?: string | null;
    icon?: string | null;
}

interface Props {
    countries: Country[];
    currencies: Currency[];
    cities: City[];
    districts: District[];
    amenities: Amenity[];
    defaults: {
        country_id?: number | null;
        currency_id?: number | null;
    };
}

export default function CommunityCreate({
    countries,
    currencies,
    cities,
    districts,
    amenities,
    defaults,
}: Props) {
    const defaultCountryId = defaults.country_id
        ? String(defaults.country_id)
        : "";
    const defaultCurrencyId = defaults.currency_id
        ? String(defaults.currency_id)
        : "";

    const { data, setData, post, processing, errors } = useForm<{
        name: string;
        country_id: string;
        currency_id: string;
        city_id: string;
        district_id: string;
        location: string;
        sales_commission_rate: string;
        rental_commission_rate: string;
        about: string;
        amenity_ids: number[];
        community_image: File | null;
        documents: File[];
    }>({
        name: "",
        country_id: defaultCountryId,
        currency_id: defaultCurrencyId,
        city_id: "",
        district_id: "",
        location: "",
        sales_commission_rate: "",
        rental_commission_rate: "",
        about: "",
        amenity_ids: [],
        community_image: null,
        documents: [],
    });

    const selectedCountry = countries.find(
        (country) => String(country.id) === data.country_id,
    );
    const selectedCity = cities.find((city) => String(city.id) === data.city_id);
    const selectedDistrict = districts.find(
        (district) => String(district.id) === data.district_id,
    );

    const getDisplayName = (item?: { name: string; name_ar?: string | null }) =>
        item?.name_ar && item.name_ar.trim() !== ""
            ? item.name_ar
            : item?.name ?? "";

    const filteredCities = data.country_id
        ? cities.filter((city) => String(city.country_id) === data.country_id)
        : cities;

    const filteredDistricts = data.city_id
        ? districts.filter(
              (district) => String(district.city_id) === data.city_id,
          )
        : [];

    const dynamicLocation = [
        getDisplayName(selectedDistrict),
        getDisplayName(selectedCity),
        getDisplayName(selectedCountry),
    ]
        .filter((part) => part !== "")
        .join(", ");

    useEffect(() => {
        if (data.location !== dynamicLocation) {
            setData("location", dynamicLocation);
        }
    }, [data.location, dynamicLocation, setData]);

    const selectedDocumentsLabel =
        data.documents.length === 0
            ? "Drop files directly here or browse from your device"
            : `${data.documents.length} file(s) selected`;

    const toggleAmenity = (amenityId: number) => {
        if (data.amenity_ids.includes(amenityId)) {
            setData(
                "amenity_ids",
                data.amenity_ids.filter((id) => id !== amenityId),
            );

            return;
        }

        setData("amenity_ids", [...data.amenity_ids, amenityId]);
    };

    const handleSubmit = (e: React.FormEvent) => {
        e.preventDefault();
        post(communitiesStore.url(), { forceFormData: true });
    };

    return (
        <>
            <Head title="Create Community" />

            <div className="flex h-full flex-1 flex-col gap-6 p-4">
                <div className="flex items-center gap-2 text-sm">
                    <span className="font-semibold text-cyan-700">Properties</span>
                    <span className="text-muted-foreground">/</span>
                    <span className="font-semibold">Add</span>
                </div>

                <Card>
                    <CardContent className="space-y-6 pt-6">
                        <div className="flex justify-end">
                            <Button asChild variant="outline" size="sm">
                                <Link href={communitiesIndex()}>
                                    <ArrowLeft className="mr-2 h-4 w-4" />
                                    Back
                                </Link>
                            </Button>
                        </div>

                        <h1 className="text-5xl font-bold tracking-tight">
                            Community Information
                        </h1>

                        <form onSubmit={handleSubmit} className="space-y-6">
                            <label
                                htmlFor="community-image"
                                className="block cursor-pointer rounded-xl border border-dashed border-gray-300 bg-gray-50 px-6 py-10 text-center"
                            >
                                <div className="mx-auto mb-4 flex h-10 w-10 items-center justify-center rounded-full border border-gray-300 bg-white">
                                    <UploadCloud className="h-5 w-5 text-gray-500" />
                                </div>
                                <p className="font-semibold text-cyan-700">
                                    Add photo
                                </p>
                                <p className="text-muted-foreground mt-2 text-sm">
                                    {data.community_image === null
                                        ? "Drop files directly here or browse from your device"
                                        : `Selected file: ${data.community_image.name}`}
                                </p>
                                <p className="text-muted-foreground mt-1 text-sm">
                                    You can upload only PNG, JPEG, JPG, WEBP, up
                                    to 1 file, up to 30 MB
                                </p>
                                <input
                                    id="community-image"
                                    type="file"
                                    className="hidden"
                                    accept=".png,.jpeg,.jpg,.webp"
                                    onChange={(e) =>
                                        setData(
                                            "community_image",
                                            e.target.files?.[0] ?? null,
                                        )
                                    }
                                />
                            </label>
                            {errors.community_image && (
                                <p className="text-destructive text-sm">
                                    {errors.community_image}
                                </p>
                            )}

                            <div className="grid gap-4 md:grid-cols-3">
                                <div className="space-y-2">
                                    <Label htmlFor="name">Community Name*</Label>
                                    <Input
                                        id="name"
                                        value={data.name}
                                        onChange={(e) =>
                                            setData("name", e.target.value)
                                        }
                                        placeholder="Enter community name"
                                    />
                                    {errors.name && (
                                        <p className="text-destructive text-sm">
                                            {errors.name}
                                        </p>
                                    )}
                                </div>

                                <div className="space-y-2">
                                    <Label htmlFor="currency_id">
                                        Property Currency*
                                    </Label>
                                    <Select
                                        value={data.currency_id}
                                        onValueChange={(value) =>
                                            setData("currency_id", value)
                                        }
                                    >
                                        <SelectTrigger>
                                            <SelectValue placeholder="Select currency" />
                                        </SelectTrigger>
                                        <SelectContent>
                                            {currencies.map((currency) => (
                                                <SelectItem
                                                    key={currency.id}
                                                    value={String(currency.id)}
                                                >
                                                    {getDisplayName(currency)} ({currency.code})
                                                </SelectItem>
                                            ))}
                                        </SelectContent>
                                    </Select>
                                    {errors.currency_id && (
                                        <p className="text-destructive text-sm">
                                            {errors.currency_id}
                                        </p>
                                    )}
                                </div>

                                <div className="space-y-2">
                                    <Label htmlFor="country_id">
                                        Country*
                                    </Label>
                                    <Select
                                        value={data.country_id}
                                        onValueChange={(value) => {
                                            const country = countries.find(
                                                (item) => String(item.id) === value,
                                            );
                                            const inferredCurrency = currencies.find(
                                                (currency) =>
                                                    currency.code ===
                                                    country?.currency_code,
                                            );

                                            setData("country_id", value);
                                            setData(
                                                "currency_id",
                                                inferredCurrency
                                                    ? String(inferredCurrency.id)
                                                    : "",
                                            );
                                            setData("city_id", "");
                                            setData("district_id", "");
                                        }}
                                    >
                                        <SelectTrigger>
                                            <SelectValue placeholder="Select country" />
                                        </SelectTrigger>
                                        <SelectContent>
                                            {countries.map((country) => (
                                                <SelectItem
                                                    key={country.id}
                                                    value={String(country.id)}
                                                >
                                                    {getDisplayName(country)}
                                                </SelectItem>
                                            ))}
                                        </SelectContent>
                                    </Select>
                                    {errors.country_id && (
                                        <p className="text-destructive text-sm">
                                            {errors.country_id}
                                        </p>
                                    )}
                                </div>
                            </div>

                            <div className="grid gap-4 md:grid-cols-3">
                                <div className="space-y-2">
                                    <Label htmlFor="city_id">City*</Label>
                                    <Select
                                        value={data.city_id}
                                        onValueChange={(value) => {
                                            setData("city_id", value);
                                            setData("district_id", "");
                                        }}
                                    >
                                        <SelectTrigger>
                                            <SelectValue placeholder="Select city" />
                                        </SelectTrigger>
                                        <SelectContent>
                                            {filteredCities.map((city) => (
                                                <SelectItem
                                                    key={city.id}
                                                    value={String(city.id)}
                                                >
                                                    {getDisplayName(city)}
                                                </SelectItem>
                                            ))}
                                        </SelectContent>
                                    </Select>
                                    {errors.city_id && (
                                        <p className="text-destructive text-sm">
                                            {errors.city_id}
                                        </p>
                                    )}
                                </div>

                                <div className="space-y-2">
                                    <Label htmlFor="district_id">District*</Label>
                                    <Select
                                        value={data.district_id}
                                        onValueChange={(value) =>
                                            setData("district_id", value)
                                        }
                                    >
                                        <SelectTrigger>
                                            <SelectValue placeholder="Select district" />
                                        </SelectTrigger>
                                        <SelectContent>
                                            {filteredDistricts.map((district) => (
                                                <SelectItem
                                                    key={district.id}
                                                    value={String(district.id)}
                                                >
                                                    {getDisplayName(district)}
                                                </SelectItem>
                                            ))}
                                        </SelectContent>
                                    </Select>
                                    {errors.district_id && (
                                        <p className="text-destructive text-sm">
                                            {errors.district_id}
                                        </p>
                                    )}
                                </div>

                                <div className="space-y-2">
                                    <Label htmlFor="location">Location*</Label>
                                    <div className="relative">
                                        <Input
                                            id="location"
                                            value={data.location}
                                            readOnly
                                            placeholder="Location is auto-filled from district, city, and country"
                                        />
                                        <ChevronDown className="text-muted-foreground pointer-events-none absolute top-1/2 right-3 h-4 w-4 -translate-y-1/2" />
                                    </div>
                                    {errors.location && (
                                        <p className="text-destructive text-sm">
                                            {errors.location}
                                        </p>
                                    )}
                                </div>
                            </div>

                            <div className="grid gap-4 md:grid-cols-2">
                                <div className="space-y-2">
                                    <Label htmlFor="sales_commission_rate">
                                        Sales Commission Rate %*
                                    </Label>
                                    <Input
                                        id="sales_commission_rate"
                                        type="number"
                                        min="0"
                                        max="100"
                                        step="0.01"
                                        value={data.sales_commission_rate}
                                        onChange={(e) =>
                                            setData(
                                                "sales_commission_rate",
                                                e.target.value,
                                            )
                                        }
                                        placeholder="e.g. 2.5"
                                    />
                                    {errors.sales_commission_rate && (
                                        <p className="text-destructive text-sm">
                                            {errors.sales_commission_rate}
                                        </p>
                                    )}
                                </div>

                                <div className="space-y-2">
                                    <Label htmlFor="rental_commission_rate">
                                        Rental Commission Rate %*
                                    </Label>
                                    <Input
                                        id="rental_commission_rate"
                                        type="number"
                                        min="0"
                                        max="100"
                                        step="0.01"
                                        value={data.rental_commission_rate}
                                        onChange={(e) =>
                                            setData(
                                                "rental_commission_rate",
                                                e.target.value,
                                            )
                                        }
                                        placeholder="e.g. 5"
                                    />
                                    {errors.rental_commission_rate && (
                                        <p className="text-destructive text-sm">
                                            {errors.rental_commission_rate}
                                        </p>
                                    )}
                                </div>
                            </div>

                            <div className="space-y-2">
                                <Label htmlFor="about">About The Project</Label>
                                <Textarea
                                    id="about"
                                    value={data.about}
                                    onChange={(e) =>
                                        setData("about", e.target.value)
                                    }
                                    placeholder="Write a short description about this community"
                                    rows={5}
                                />
                                {errors.about && (
                                    <p className="text-destructive text-sm">
                                        {errors.about}
                                    </p>
                                )}
                            </div>

                            <div className="space-y-3 rounded-xl border border-gray-200 p-4">
                                <div>
                                    <h2 className="text-lg font-semibold">
                                        Amenities
                                    </h2>
                                    <p className="text-muted-foreground text-sm">
                                        Select the amenities available in this
                                        community
                                    </p>
                                </div>

                                {amenities.length === 0 ? (
                                    <p className="text-muted-foreground text-sm">
                                        No amenities available.
                                    </p>
                                ) : (
                                    <div className="grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
                                        {amenities.map((amenity) => {
                                            const checked = data.amenity_ids.includes(
                                                amenity.id,
                                            );

                                            return (
                                                <label
                                                    key={amenity.id}
                                                    className={`flex cursor-pointer items-center gap-3 rounded-lg border p-3 transition-colors ${
                                                        checked
                                                            ? "border-primary bg-primary/5"
                                                            : "border-gray-200"
                                                    }`}
                                                >
                                                    <Checkbox
                                                        checked={checked}
                                                        onCheckedChange={() =>
                                                            toggleAmenity(
                                                                amenity.id,
                                                            )
                                                        }
                                                    />
                                                    {amenity.icon && (
                                                        <img
                                                            src={amenity.icon}
                                                            alt={amenity.name}
                                                            className="h-7 w-7 rounded"
                                                            loading="lazy"
                                                        />
                                                    )}
                                                    <span className="text-sm font-medium">
                                                        {amenity.name_ar ??
                                                            amenity.name}
                                                    </span>
                                                </label>
                                            );
                                        })}
                                    </div>
                                )}
                                {errors.amenity_ids && (
                                    <p className="text-destructive text-sm">
                                        {errors.amenity_ids}
                                    </p>
                                )}
                            </div>

                            <div className="space-y-2 rounded-xl border border-gray-200 p-4">
                                <h2 className="text-lg font-semibold">
                                    Documents
                                </h2>
                                <p className="text-muted-foreground text-sm">
                                    Upload user guides, warranties, and related
                                    files for this community.
                                </p>
                                <label
                                    htmlFor="documents"
                                    className="block cursor-pointer rounded-lg border border-dashed border-gray-300 bg-gray-50 px-4 py-8 text-center"
                                >
                                    <div className="mx-auto mb-2 flex h-9 w-9 items-center justify-center rounded-full border border-gray-300 bg-white">
                                        <UploadCloud className="h-4 w-4 text-gray-500" />
                                    </div>
                                    <p className="font-medium text-cyan-700">
                                        Upload Documents
                                    </p>
                                    <p className="text-muted-foreground mt-2 text-sm">
                                        {selectedDocumentsLabel}
                                    </p>
                                    <p className="text-muted-foreground mt-1 text-sm">
                                        Allowed types: PDF, DOC, DOCX, XLS,
                                        XLSX, PPT, PPTX, PNG, JPEG, JPG, WEBP.
                                        Max 10 files, 30 MB each.
                                    </p>
                                    <input
                                        id="documents"
                                        type="file"
                                        multiple
                                        className="hidden"
                                        accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.png,.jpeg,.jpg,.webp"
                                        onChange={(e) =>
                                            setData(
                                                "documents",
                                                Array.from(
                                                    e.target.files ?? [],
                                                ),
                                            )
                                        }
                                    />
                                </label>
                                {errors.documents && (
                                    <p className="text-destructive text-sm">
                                        {errors.documents}
                                    </p>
                                )}
                            </div>

                            <div className="flex justify-end gap-3 border-t pt-4">
                                <Button
                                    type="button"
                                    variant="outline"
                                    onClick={() => window.history.back()}
                                >
                                    Cancel
                                </Button>
                                <Button type="submit" disabled={processing}>
                                    {processing ? "Saving..." : "Save"}
                                </Button>
                            </div>
                        </form>
                    </CardContent>
                </Card>
            </div>
        </>
    );
}

CommunityCreate.layout = {
    breadcrumbs: [
        { title: "Properties", href: communitiesIndex() },
        { title: "Add", href: "/properties-list/new/community" },
    ],
};
