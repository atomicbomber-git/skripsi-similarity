import React from 'react';
import ReactDOM from 'react-dom';
import axios from "axios"

export default class MostSimilarSkripsisIndex extends React.Component {
    constructor(props) {
        super(props);
        this.state = {
            isFetchingData: true,
            skripsiSimilarityRecords: [],
        }
    }

    componentDidMount() {
        this.fetchData();
    }

    fetchData() {
        axios.get(this.props.dataUrl)
            .then(response => {
                this.setState({
                    skripsiSimilarityRecords: response.data.data,
                })
            }).catch(error => {
            this.setState({
                skripsiSimilarityRecords: [],
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
        if (this.state.skripsiSimilarityRecords.length === 0) {
            return (
                <div className="alert alert-warning">
                    Maaf, tidak terdapat data sama sekali.
                </div>
            )
        }

        return (
            <div className="table-responsive table-wrapper-scrollable">
                <table className="table table-sm table-striped table-hover">
                    <thead>
                        <tr>
                            <th> # </th>
                            <th> Nama </th>
                            <th> Skripsi </th>
                            <th className="text-right"> Similarity </th>
                        </tr>
                    </thead>

                    <tbody>
                    {this.state.skripsiSimilarityRecords.map((skripsiSimilarityRecord, index) => (
                        <tr key={index}>
                            <td> { index + 1 } </td>
                            <td> { skripsiSimilarityRecord?.skripsi?.mahasiswa?.name } </td>
                            <td> { skripsiSimilarityRecord?.skripsi?.judul } </td>
                            <td className="text-right"> { (skripsiSimilarityRecord.diceSimilarity * 100).toFixed(2) } % </td>
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

const root = document.getElementById("most-similar-skripsis-index")
if (root) {
    ReactDOM.render(
        <MostSimilarSkripsisIndex
            {...root.dataset}
        />,
        root
    )
}
