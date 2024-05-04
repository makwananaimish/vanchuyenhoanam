async function send(orderId, content) {
    return $.post(`/messages`, {
        order_id: orderId,
        _token: getCsrfToken(),
        content,
    });
}

async function getMessages(orderId) {
    return $.get(`/messages/${orderId}`);
}

function buildMessagesHTML(messages) {
    return `<div class="overflow-auto" style="max-height:200px; background-color: #f0f3f5;">
                <table class="table border rounded" >
                                        <tbody>
                                            ${messages
                                                .map(
                                                    (x) => `
                                            <tr>
                                                <td>
                                                    <div class="d-flex justify-content-between">
                                                        <span>${x.sender}</span>
                                                        <span>:</span>
                                                    </div>
                                                </td>
                                                <td class="text-wrap">${x.content}</td>
                                            </tr>`
                                                )
                                                .join("\n")}
                                        </tbody>
                                    </table>
                                </div>`;
}

async function _send(thiz, orderId, content) {
    $(thiz).prop("disabled", true);
    $(thiz).html(
        `<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Đang gửi...`
    );

    await send(orderId, content);

    const messages = await getMessages(orderId);
    $(`#messages-${orderId}`).html(buildMessagesHTML(messages));
    $(`#content-${orderId}`).val("");

    $(thiz).prop("disabled", false);
    $(thiz).html("Gửi");
}

$(".message-modal").on("shown.bs.modal", async function () {
    const orderId = $(this).attr("data-id");
    const messages = await getMessages(orderId);
    $(`#messages-${orderId}`).html(buildMessagesHTML(messages));
});

$('*[data-unseen-messages="0"]').hide();
