import React from 'react';
import ReactDOM from 'react-dom';
import axios from "axios"

class MostSimilarSentencesIndex extends React.Component {
    constructor(props) {
        super(props);
        this.state = {
            isFetchingData: true,
            kalimatSimilarities: [],
        }
    }

    componentDidMount() {
        this.fetchData();
    }

    fetchData() {
        axios.get(this.props.dataUrl)
            .then(response => {
                this.setState({
                    kalimatSimilarities: response.data,
                })
            }).catch(error => {
            this.setState({
                kalimatSimilarities: [],
            })
        }).finally(() => {
            this.setState({
                isFetchingData: false
            })
        })
    }

    loadingContent() {
        return (
            <div className="alert alert-info">
                Loading...
            </div>
        )
    }

    mainContent() {
        return (
            <div className="table-responsive table-wrapper-scrollable">
                <table className="table table-sm table-striped table-hover">
                    <thead>
                    <tr>
                        <th> #</th>
                        <th> Kalimat di Skripsi Anda</th>
                        <th> Kalimat di Skripsi Lain</th>
                    </tr>
                    </thead>

                    <tbody>
                    {this.state.kalimatSimilarities.map((kalimatSimilarity, index) => (
                        <tr>
                            <td> {index + 1} </td>
                            <td> {kalimatSimilarity.teks_a} </td>
                            <td>
                                {kalimatSimilarity.teks_b}
                                <br/>
                                <span className="small-skripsi-title">
                                        <strong> {kalimatSimilarity.skripsi.judul} / {kalimatSimilarity.skripsi.mahasiswa.nama} </strong>
                                    </span>
                            </td>
                        </tr>
                    ))}
                    </tbody>
                </table>
            </div>
        )
    }

    render() {
        return this.state.isFetchingData ?
            this.loadingContent() :
            this.mainContent()
    }
}

export default MostSimilarSentencesIndex;

const root = document.getElementById("most-similar-sentences-index")
if (root) {
    ReactDOM.render(
        <MostSimilarSentencesIndex
            {...root.dataset}
        />,
        root
    )
}
