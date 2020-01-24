class Form extends React.Component {
    constructor(props){
        super(props);

        this.state = {causes: [], start: false};
    }

    componentDidMount() {
        fetch("/wp-content/plugins/feedback/inquiries.php?inquiries=causes")
            .then(response => response.json())
            .then(commits => {
            this.setState({causes: commits, start: true});
        });
    }

    render() {
        return(
            <form enctype="multipart/form-data" action="/wp-content/plugins/feedback/form-load.php" method="POST">
                <p>
                    Ваше имя:
                    <input type="text" name="name" />
                </p>
                <p>
                    Адрес электронной почты:
                    <input type="text" name="email" />
                </p>
                <p>
                    Причина:
                    <select name="causes" style={{width: "100%"}}>
                        {
                            this.state.start ? (
                                this.state.causes.map(item =>
                                    <option value={item.id}>{item.subject}</option>
                                )
                            ) : (
                                null
                            )
                        }
                    </select>
                </p>
                <p>
                    Описание:
                    <textarea name="comment" cols="45" rows="8" style={{resize: "none"}}></textarea>
                </p>
                <p>
                    <input name="userfile" type="file" accept=".jpg, .png, .gif" />
                </p>
                <p>
                    <input name="redirect" type="hidden" value={location.href} />
                    <input type="submit" value="Отправить" />
                </p>
            </form>
        );
    }
}

ReactDOM.render(
    <Form />,
    document.getElementById("app")
)
