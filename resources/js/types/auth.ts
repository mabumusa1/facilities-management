export type User = {
    id: number;
    name: string;
    email: string;
    avatar?: string;
    email_verified_at: string | null;
    created_at: string;
    updated_at: string;
    [key: string]: unknown;
};

export type Auth = {
    user: User;
};

export type TwoFactorConfigContent = {
    title: string;
    description: string;
    buttonText: string;
};

export type SessionAgent = {
    browser: string;
    platform: string;
    device: string;
};

export type ActiveSession = {
    id: string;
    agent: SessionAgent;
    ip_address: string;
    location?: string;
    last_activity: number;
    last_activity_diff: string;
    is_current: boolean;
};
