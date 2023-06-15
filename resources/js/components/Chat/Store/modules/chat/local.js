function getKey(chatId){
	return `JobtronChatMessages-${chatId}`
}

export function hasLocal(chatId){
	return !!localStorage.getItem(getKey(chatId))
}
export function loadLocal(chatId){
	return JSON.parse(localStorage.getItem(getKey(chatId)) || '[]')
}
export function saveLocal(chatId, messages){
	localStorage.setItem(getKey(chatId), JSON.stringify(messages.slice(-20).map(msg => {
		const short = {
			body: msg.body,
			chat_id: msg.chat_id,
			created_at: msg.created_at,
			deleted: msg.deleted,
			files: msg.files,
			id: msg.id,
			parent_id: msg.parent_id,
			pinned: msg.pinned,
			sender_id: msg.sender_id,
			readers: msg.readers.map(reader => ({
				deleted_at: reader.deleted_at,
				id: reader.id,
				img_url: reader.img_url,
				last_name: reader.last_name,
				last_seen: reader.last_seen,
				name: reader.name,
			})),
			sender: {
				deleted_at: msg.sender.deleted_at,
				id: msg.sender.id,
				img_url: msg.sender.img_url,
				last_name: msg.sender.last_name,
				last_seen: msg.sender.last_seen,
				name: msg.sender.name,
			},
		}
		if(msg.event){
			short.event = {
				id: msg.event.id,
				message_id: msg.event.message_id,
				type: msg.event.type,
				payload: {
					user: msg.event.payload.user ? {
						id: msg.event.payload.user.id,
						name: msg.event.payload.user.name,
					} : undefined,
					chat: msg.event.payload.chat ? {
						title: msg.event.payload.chat.title,
					} : undefined,
				},
			}
		}
		return short
	})))
}
