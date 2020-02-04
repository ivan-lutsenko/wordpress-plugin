/**
 * ReactJS file
 *
 * @category Plugin
 * @package  WordPress plugin
 */

/** ReactJS file */
class Form extends React.Component {
	constructor(props) {
		super(props);
		this.state = {causes: [], token: '' , start: false};
		this.viewMessages = this.viewMessages.bind(this);
	}
	componentDidMount() {
		fetch("/wp-content/plugins/feedback/token.php")
			.then(response => response.text())
			.then(commits => this.setState({token: commits, start: true}));
	}
	viewMessages() {
		fetch("/wp-content/plugins/feedback/inquiries.php?inquiries=causes&token=" + this.state.token)
			.then(response => response.json())
			.then(commits => { this.setState( {causes: commits, start: false} ); } );
	}
	render() {
		if (this.state.start) {
			this.viewMessages();
		}
		return(
			<form enctype = "multipart/form-data" action = "/wp-content/plugins/feedback/inquiries.php" method = "POST" >
				<p>
					Ваше имя:
					<input type = "text" name = "name" />
				</p>
				<p>
					Адрес электронной почты:
					<input type = "text" name = "email" />
				</p>
				<p>
					Причина:
					<select name = "causes" style = {{width: "100%"}} >
					{
						this.state.causes.map(
							item =>
							<option value = {item.id} > {item.subject} </option>
						)
					}
					</select>
				</p>
				<p>
					Описание:
					<textarea name = "comment" cols = "45" rows = "8" style = {{resize: "none"}} > </textarea>
				</p>
				<p>
					<input name = "userfile" type = "file" accept = ".jpg, .png, .gif" />
				</p>
				<p>
					<input name = "redirect" type = "hidden" value = {location.href} />
					<input name = "token" type = "hidden" value = {this.state.token} />
					<input type = "submit" value = "Отправить" />
				</p>
			</form>
		);
	}
}

ReactDOM.render(
	< Form /> ,
	document.getElementById( "app" )
)
