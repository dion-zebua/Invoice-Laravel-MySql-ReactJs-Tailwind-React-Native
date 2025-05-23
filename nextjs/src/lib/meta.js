export default async function updateMetadata(updates) {

    const getMetadata = {
        title: `Pembuatan Invoice Online - ${process.env.NEXT_PUBLIC_APP_NAME}`,
        description: `Buat invoice anda dengan mudah sekarang juga! - ${process.env.NEXT_PUBLIC_APP_NAME}`,
        applicationName: process.env.NEXT_PUBLIC_APP_NAME,
        authors: [{ name: "Dion Zebua", url: "https://dionzebua.com" }],
        publisher: "Dion Zebua",
        openGraph: {
            siteName: process.env.NEXT_PUBLIC_APP_NAME,
            title: `Pembuatan Invoice Online - ${process.env.NEXT_PUBLIC_APP_NAME}`,
            description: `Buat invoice anda dengan mudah sekarang juga! - ${process.env.NEXT_PUBLIC_APP_NAME}`,
            url: process.env.NEXT_PUBLIC_APP_URL_FRONTEND,
            images: [
                {
                    url: `${process.env.NEXT_PUBLIC_APP_URL_FRONTEND}image/invoice-logo.jpg`,
                    width: 500,
                    height: 500,
                },
            ],
            locale: "id_ID",
            type: "website",
        },
        robots: {
            index: false,
            follow: false,
            nocache: false,
            googleBot: {
                index: false,
                follow: false,
                noimageindex: false,
                "max-video-preview": -1,
                "max-image-preview": "large",
                "max-snippet": -1,
            },
        },
    };

    return {
        ...getMetadata,
        ...updates,
        openGraph: {
            ...getMetadata.openGraph,
            ...updates.openGraph,
        },
        robots: {
            ...getMetadata.robots,
            ...updates.robots,
            googleBot: {
                ...getMetadata.robots?.googleBot,
                ...updates.robots?.googleBot,
            }
        },
    };
};
