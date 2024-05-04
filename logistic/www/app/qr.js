const { VietQR } = require("vietqr");
const args = require("args-parser")(process.argv);

let vietQR = new VietQR({
    clientID: "de8a0804-a76d-41e5-8ad6-31503ce7d5f4",
    apiKey: "17c29f09-4ea2-4417-b9c2-7f020d35de42",
});

const genQRCodeBase64 = async ({
    bank,
    accountName,
    accountNumber,
    amount,
    memo,
}) => {
    const resp = await vietQR.genQRCodeBase64({
        bank: bank || "970415",
        accountName: accountName || "LAM XUAN DONG",
        accountNumber: accountNumber || "105877602150",
        amount,
        memo,
        template: "compact",
    });

    return resp.data;
};

const genQuickLink = async ({
    bank,
    accountName,
    accountNumber,
    amount,
    memo,
}) => {
    const resp = await vietQR.genQuickLink({
        bank: bank || "970415",
        accountName: accountName || "LAM XUAN DONG",
        accountNumber: accountNumber || "105877602150",
        amount,
        memo,
        template: "compact",
        media: ".jpg",
    });

    return resp;
};

(async () => {
    console.log(JSON.stringify(await genQuickLink(args)));
})();
