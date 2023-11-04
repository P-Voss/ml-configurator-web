export default {
    en: {
        button: {
            addDense: "Add layer",
            addLstm: "Add LSTM layer",
            addGru: "Add GRU layer",
            addDropout: "Add Dropout layer",
            save: "save",
        },
        label: {
            name: "Name",
            description: "Description",
            neuralnetIntro: "Die Architektur eines neuronalen Netzes ist ein entscheidender Faktor für dessen Leistung und Anpassungsfähigkeit. Jede Schicht des Netzes hat einen speziellen Zweck und kann dazu beitragen, unterschiedliche Aspekte deiner Daten zu modellieren. Hier wirst du die Möglichkeit haben, deine eigene Netzwerkarchitektur Schicht für Schicht aufzubauen.",
            dropoutIntro: "Manchmal wird Dropout als Parameter in anderen Layern verwendet und manchmal als eigenständiger Layer. Der Unterschied liegt hauptsächlich in der Implementierung und Flexibilität. Durch das Hinzufügen eines eigenen Dropout-Layers können Sie den Dropout gezielt auf bestimmte Schichten anwenden und die Architektur des Netzwerks anpassen. Ein Dropout-Parameter innerhalb eines anderen Layers bietet eine kompaktere Möglichkeit, Dropout für diesen speziellen Layer hinzuzufügen.",

            dropout: "Dropout-Rate",

            denseNeurons: "neurons",
            denseActivation: "activation function",
            denseRegularization: "regularization",

            recNeurons: "units (neurons)",
            recActivation: "activation function",
            recRegularization: "regularization",
            recDropout: "recurrent dropout",
            recSequence: "return sequence",

            dtreeMaxDepth: "max depth",
            dtreeMaxFeatures: "max features",
            dtreeMinSampleSplit: "min samples per split",
            dtreeMinSamplesLeaf: "min samples per leaf",
            dtreeMissingValues: "handling of missing values",
            dtreeQuality: "evaluation",

            svmC: "C (regularization)",
            svmDegree: "Degree (for polynomial kernel)",

            linregRegularization: "regularization",
            logregRegularization: "regularization",
        },
        option: {
            dtreeMean: "average",
            dtreeMedian: "median",
            dtreeDrop: "drop",
            dtreeGini: "Gini",
            dtreeEntropy: "Entropy",

            linregRegularizationNone: "None",
            linregRegularizationL1: "L1 regularization (Lasso)",
            linregRegularizationL2: "L2 regularization (Ridge)",
        },
        helptext: {
            denseneuron: "Bestimmt die Anzahl der Neuronen in dieser Schicht. Mehr Neuronen erlauben komplexere Modelle, können aber auch zu Overfitting führen.",
            lstmNeuron: "In LSTM-Layern bezieht sich der Begriff 'Einheiten' auf die Anzahl der LSTM-Zellen in der Schicht. Jede Einheit enthält den Mechanismus des LSTM, einschließlich der verschiedenen Gates und des internen Zellzustands. Sie können sich eine Einheit grob als ein 'erweitertes Neuron' vorstellen, das speziell für sequenzielle Daten entwickelt wurde.",
            gruNeuron: "In GRU-Layern bezieht sich der Begriff 'Einheiten' auf die Anzahl der GRU-Zellen in der Schicht. Jede Einheit enthält den Mechanismus des GRU, einschließlich der verschiedenen Gates. Sie können sich eine Einheit grob als ein 'erweitertes Neuron' vorstellen, das speziell für sequenzielle Daten entwickelt wurde.",
            activationFunction: "Definiert, wie die Ausgabe eines Neurons berechnet wird.",
            activationFunctionGru: "Definiert, wie die Ausgabe eines Neurons berechnet wird. Typischerweise wird Tanh für GRU-Aktivierungen verwendet. ReLU kann in manchen Fällen funktionieren, birgt aber das Risiko des Gradienten-Explodierens.",
            activationFunctionLstm: "Definiert, wie die Ausgabe eines Neurons berechnet wird. Typischerweise wird Tanh für LSTM-Aktivierungen verwendet. ReLU kann in manchen Fällen funktionieren, birgt aber das Risiko des Gradienten-Explodierens.",
            activationFunctionRelu: "Beliebt für Zwischenschichten, da es schnelles Training ermöglicht und nicht saturiert. Es setzt alle negativen Werte auf null und behält positive Werte bei. Aber Vorsicht vor \"toten\" Neuronen, die nie aktiviert werden. ",
            activationFunctionSigmoid: "Begrenzt Ausgaben zwischen 0 und 1. Sie eignet sich besonders gut für Ausgabeschichten von binären Klassifikationsproblemen. Wegen ihrer sättigenden Natur und der Gefahr des verschwindenden Gradienten nicht für tiefe Netzwerke empfohlen.",
            activationFunctionTanh: "Wie Sigmoid, aber mit Ausgaben zwischen -1 und 1. Es zentriert die Daten, was oft vorteilhaft ist, aber es kann auch saturieren.",
            regularizationTypeNet: "Reduziert Overfitting durch Bestrafen großer Gewichtswerte.",
            regularizationTypeNetL1: "Bestraft die absolute Größe der Gewichte; kann zu spärlichen Gewichtsmatrizen führen.",
            regularizationTypeNetL2: "Bestraft das Quadrat der Gewichtsgrößen; gebräuchlicher als L1.",
            regularizationTypeNetLambda: "Der Lambda-Wert steuert die Stärke der Regularisierung. Ein höherer Wert führt zu stärkerer Regularisierung, wodurch das Modell vorsichtiger wird und große Gewichtswerte vermeidet. Dies kann Overfitting reduzieren, aber ein zu hoher Wert kann zu Underfitting führen. Beginnen Sie mit einem kleinen Wert (z.B. 0,001) und justieren Sie bei Bedarf.",
            dropout: "Ein Mechanismus gegen Overfitting. Ein bestimmter Prozentsatz der Neuronen wird zufällig in jedem Trainingsschritt \"ausgeschaltet\".",

            lstmSequence: "Aktivieren Sie diese Option, wenn Sie die gesamte Sequenz der Ausgaben anstelle der letzten Ausgabe zurückgeben möchten. Dies ist besonders nützlich, wenn Sie eine zeitliche Abfolge an einen anderen LSTM- oder rekurrenten Layer weitergeben möchten. Wenn Sie beispielsweise ein Modell für die Satzgenerierung oder die Vorhersage von Zeitreihen mit mehreren zukünftigen Punkten erstellen, benötigen Sie die vollständige Sequenz.",
            gruSequence: "Aktivieren Sie diese Option, wenn Sie die gesamte Sequenz der Ausgaben anstelle der letzten Ausgabe zurückgeben möchten. Dies ist besonders nützlich, wenn Sie eine zeitliche Abfolge an einen anderen GRU- oder rekurrenten Layer weitergeben möchten. Wenn Sie beispielsweise ein Modell für die Satzgenerierung oder die Vorhersage von Zeitreihen mit mehreren zukünftigen Punkten erstellen, benötigen Sie die vollständige Sequenz.",

            recDropout: "Während \"Dropout\" die Eingabeverbindungen zu den LSTM-Einheiten beeinflusst, betrifft \"rekurrenter Dropout\" die rekurrenten Verbindungen innerhalb der Einheiten. Dies kann dabei helfen, Overfitting in zeitlichen Sequenzen zu verhindern.",

            dtreeMaxDepth: "Die maximale Tiefe des Baumes. Ein tieferer Baum wird genauer sein, aber das Risiko des Overfittings steigt.",
            dtreeMaxFeatures: "Die maximale Anzahl von Merkmalen, die für das beste Teilen gesucht werden. Ein kleinerer Wert kann den Algorithmus schneller machen.",
            dtreeMinSampleSplit: "Dieser Parameter gibt die minimale Anzahl von Datensätzen an, die benötigt wird, um einen Knoten im Baum zu teilen. Wenn die Anzahl der Datensätze in einem Knoten unter diesem Wert liegt, wird der Knoten nicht weiter geteilt. Dies hilft, kleine und feinverzweigte Bäume zu verhindern, die zu Überanpassung führen können.",
            dtreeMinSamplesLeaf: "Die minimale Anzahl von Datensätzen, die in einem Blattknoten (Endknoten) des Baums enthalten sein müssen. Das Erhöhen dieser Zahl kann das Modell stabiler und weniger anfällig für Rauschen in den Daten machen. Allerdings kann ein zu hoher Wert die Fähigkeit des Modells einschränken, feinere Muster zu erkennen.",
            dtreeMissingValues: "Bestimmt, wie mit fehlenden Werten umgegangen wird. Sie können entweder durch den Mittelwert, den Median ersetzt oder komplett verworfen werden.",
            dtreeQuality: "Das Maß, das verwendet wird, um die Qualität eines Splits zu bewerten. Gini-Unreinheit und Entropie sind die gängigsten Methoden.",
            dtreeGini: "Die Gini-Unreinheit misst, wie oft ein zufällig ausgewähltes Element fälschlicherweise klassifiziert werden würde, wenn es zufällig nach der Verteilung der Labels in einem Knoten klassifiziert wird. Eine Gini-Unreinheit von 0 bedeutet, dass alle Elemente im Knoten zu einer einzelnen Klasse gehören, d.h. der Knoten ist \"rein\". Ein höherer Wert für die Gini-Unreinheit zeigt eine größere Vermischung von Klassen in einem Knoten an. Bei der Entscheidungsbaum-Bildung versucht man, Attribute zu finden, die die Gini-Unreinheit minimieren.",
            dtreeEntropy: "Entropie ist ein Maß für die Unordnung oder den Informationsgehalt. In Bezug auf Entscheidungsbäume misst die Entropie, wie gemischt die Daten in Bezug auf das Label in einem Knoten sind. Ein Knoten mit einer Entropie von 0 ist völlig \"rein\", d.h. alle Datenpunkte in diesem Knoten gehören zu einer Klasse. Ein Knoten mit einer höheren Entropie enthält eine Mischung von Klassen. Bei der Entscheidungsbaum-Bildung wird versucht, Attribute zu finden, die die Entropie minimieren, wodurch der Informationsgewinn maximiert wird.",

            svmKernel: "Der Kernel bestimmt den Typ der Hyperebene, die zur Trennung der Daten verwendet wird. Unterschiedliche Kernel können verschiedene Formen von Trennungen erzeugen.",
            svmKernelLinear: "Dieser Kernel erstellt eine lineare Trennlinie. Er ist ideal für Daten, bei denen die Klassen durch eine gerade Linie oder Ebene getrennt werden können.",
            svmKernelPolynomial: "Dieser Kernel erstellt eine polynomiale Trennlinie, was nützlich ist, wenn die Daten in einer polynomialen Form getrennt werden müssen.",
            svmKernelRBF: "Dies ist ein häufig verwendeter Kernel, der es ermöglicht, eine nichtlineare Trennung in der Form einer Glockenkurve zu erstellen. Er ist besonders nützlich, wenn die Trennung der Daten nicht einfach ist.",
            svmKernelSigmoid: "Dieser Kernel erzeugt eine trennende Hyperplane in der Form einer Sigmoid-Funktion.",
            svmC: "Der Regularisierungsparameter C ist ein Trade-off zwischen der Erzielung eines möglichst großen Abstands zwischen der trennenden Hyperplane und den Datenpunkten und der Minimierung der Klassifikationsfehler. Ein kleiner Wert von C versucht, die Margin zwischen den Datenpunkten und der trennenden Linie so groß wie möglich zu halten, was zu einer glatteren Entscheidungsgrenze führen kann, während es einige Misclassifikationen zulässt. Ein hoher Wert von C legt mehr Wert darauf, alle Datenpunkte korrekt zu klassifizieren, kann jedoch zu Overfitting führen, da die Entscheidungsgrenze zu stark an die Trainingsdaten angepasst wird.",
            svmDegree: "Der Grad des Polynomial-Kernels bestimmt die Art der polynomialen Trennung zwischen den Daten. Ein höherer Grad führt zu komplexeren Trennungen. Es ist wichtig zu beachten, dass ein zu hoher Grad zu Overfitting führen kann, da die Entscheidungsgrenze zu genau an die Trainingsdaten angepasst wird. Der Degree-Parameter wird nur verwendet, wenn der Kernel auf \"Polynomial\" gesetzt ist.",
            svmDegreeHint: "Degree wird nur beim Polynomial-Kernel verwendet.",

            linregRegularization: "Der Regularisierungstyp steuert die Art der Strafe, die auf das Modell angewendet wird, um Overfitting zu vermeiden.",
            linregRegularizationL1: "Fördert das Modell, dünn besetzte Gewichte zu lernen, wodurch irrelevante oder schwach wirkende Features eliminiert werden können.",
            linregRegularizationL2: "Fügt der Fehlerfunktion eine Strafterm hinzu, die proportional zum Quadrat der Summe der Gewichte ist. Dies führt dazu, dass die Gewichte kleiner werden, aber nicht auf Null reduziert werden.",
            linregAlpha: "Der Alpha-Parameter kontrolliert die Stärke der Regularisierung. Ein höherer Alpha-Wert führt zu einer stärkeren Regularisierung. Die optimale Wahl von Alpha ist datenabhängig und sollte durch Cross-Validation bestimmt werden.",

            logregRegularization: "Der Regularizer Typ bestimmt die Art der Regularisierung, die auf die Kostenfunktion angewendet wird.",
            logregRegularizationL1: "L1-Regularisierung fügt einen Strafterm hinzu, der proportional zur absoluten Größe der Gewichte ist.",
            logregRegularizationL2: "L2-Regularisierung fügt einen Strafterm hinzu, der proportional zum Quadrat der Gewichte ist.",
            logregSolver: "Der Solver bestimmt die Optimierungsalgorithmen, die zur Lösung des Optimierungsproblems verwendet werden. Die Auswahl des Solvers kann sich auf die Konvergenzgeschwindigkeit und die Leistung der logistischen Regression auswirken.",
            logregSolverNewton: "Ein numerischer Optimierungsalgorithmus, der auf der Berechnung der exakten Hesse-Matrix basiert. Er ist für kleine Datensätze geeignet, aber möglicherweise nicht für große Datensätze aufgrund der Berechnungskomplexität der Hesse-Matrix.",
            logregSolverL: "Ein quasi-newtonsches Optimierungsverfahren, das auf einer begrenzten Speicherimplementierung des BFGS-Verfahrens basiert. Es ist in der Regel effizienter als der Newton-CG-Solver für große Datensätze.",
            logregSolverLiblinear: "Eine der populärsten Bibliotheken für lineare Modelle, die lineare SVMs und logistische Regression für große Datensätze effizient unterstützt. Es verwendet eine verbesserte Version des Coordinate Descent-Verfahrens.",
            logregSolverSAG: "Stochastic Average Gradient Descent ist eine Variante des Gradientenabstiegs, die für große Datensätze geeignet ist. Sie arbeitet schneller als normale Gradientenabstiegsverfahren, indem sie den Durchschnitt der Gradienten über mehrere zufällige Batches berechnet.",
            logregSolverSAGA: "Eine Verbesserung des SAG-Verfahrens, das eine zusätzliche Korrektur für die Richtung des Gradienten einführt, um die Konvergenzgeschwindigkeit zu erhöhen. Es ist gut für konvexe Probleme und hat eine schnelle Konvergenz.",
            logregLambda: "Lambda ist ein Hyperparameter, der nur für Modelle mit Regularisierung relevant ist. Er kontrolliert die Stärke der Regularisierung. Ein höherer Wert von Lambda kann dazu führen, dass die Koeffizienten näher oder gleich Null werden, was zu einer stärkeren Regularisierung führt.",

            initModelchangeHint: "Modeltype can not be changed later on.",
        },
        headline: {
            tasktype: "Tasktype",
            modeltype: "Modeltype",
            layertype: "Add a layer",
            architecture: "Architecture",
        },
        card: {
            classificationLabel: "Classification",
            classificationDescription: "Die Klassifizierung ist ein Überwachtes Lernen, bei dem das Ziel darin besteht, eine Eingabe (wie ein Bild, Text oder ein anderer Datentyp) einer der vordefinierten Kategorien zuzuordnen. Beispielsweise könnte man versuchen, Bilder von Tieren als \"Hund\", \"Katze\" oder \"Vogel\" zu klassifizieren.",
            regressionLabel: "Regression",
            regressionDescription: "Regressionsanalysen versuchen, den Zusammenhang zwischen Eingabevariablen (Merkmale) und einer kontinuierlichen Ausgabevariable (Ziel) zu modellieren. Zum Beispiel könnte man den Preis eines Hauses basierend auf Merkmalen wie Größe, Lage und Alter vorhersagen.",

            dtreeLabel: "Decision Tree",
            dtreeSubLabel: "(Entscheidungsbaum)",
            dtreeDescription: "Entscheidungsbäume teilen Daten rekursiv in Untergruppen, um Entscheidungen auf der Grundlage von Eingabemerkmalen zu treffen.",

            logregLabel: "Logistical Regression",
            logregDescription: "Dieses Modell schätzt die Wahrscheinlichkeit, dass eine gegebene Eingabe zu einer bestimmten Klasse gehört. Es ist besonders nützlich für binäre Klassifizierungsaufgaben, kann aber auch für mehrklassige Aufgaben verwendet werden.",

            svmLabel: "SVM",
            svmSubLabel: "(Support Vector Machines)",
            svmDescription: "Ein Modell, das versucht, eine optimale Trennlinie oder -fläche zwischen Datenklassen zu finden. Es ist besonders nützlich für komplexe Klassifikationsprobleme, kann aber auch für Regression verwendet werden.",

            linregLabel: "Linear Regression",
            linregDescription: "Dieses Modell versucht, einen linearen Zusammenhang zwischen den Eingabemerkmalen und dem Zielwert zu finden.",

            nnLabel: "Neural Network",
            nnDescription: "Ein komplexes Modell, das aus vielen miteinander verbundenen \"Neuronen\" besteht. Es kann nicht-lineare Zusammenhänge modellieren und ist für eine Vielzahl von Aufgaben geeignet, einschließlich Regressionsproblemen.",

            rnnLabel: "RNN",
            rnnSubLabel: "(Recurrent Neural Network)",
            rnnDescription: "Eine spezielle Art von neuronalem Netzwerk, das für sequentielle Daten konzipiert ist. Es ist besonders nützlich für Zeitreihen- oder Textdaten.",

            denseLabel: "Dense",
            denseSubLabel: "(fully connected)",
            denseDescription: "Dies sind die Standard-Schichten, die du in den meisten neuronalen Netzen findest. Jedes Neuron in einer Dense-Schicht ist mit jedem Neuron in der vorherigen Schicht verbunden.",
            lstmLabel: "LSTM",
            lstmSubLabel: "(Long Short-Term Memory)",
            lstmDescription: "Spezialisierte rekurrente Schicht, die langfristige Abhängigkeiten in sequentiellen Daten modelliert. Ideal für Zeitreihen und Aufgaben mit zeitlichen Abhängigkeiten durch ihre \"Gates\", die entscheiden, welche Informationen gespeichert oder verworfen werden.",
            gruLabel: "GRU",
            gruSubLabel: "(Gated Recurrent Unit)",
            gruDescription: "Rekurrente Schicht ähnlich wie LSTM, jedoch mit einfacherer Struktur. Kombiniert bestimmte Gates für Effizienz und ist oft schneller im Training. Gut für sequentielle Mustererkennung.",
            dropoutLabel: "Dropout",
            dropoutDescription: "Eine Technik zur Reduzierung von Overfitting, bei der zufällig ausgewählte Neuronen während des Trainingsprozesses \"ausgeschaltet\" werden.",
        },
        navigation: {
            modelselection: "Model Selection",
            architecture: "Architecture",
            training: "Training",
        }
    },
    de: {
        button: {
            addDense: "Schicht hinzufügen",
            addLstm: "LSTM-Schicht hinzufügen",
            addGru: "GRU-Schicht hinzufügen",
            addDropout: "Dropout-Schicht hinzufügen",
            save: "speichern",
        },
        label: {
            name: "Name",
            description: "Beschreibung",
            neuralnetIntro: "Die Architektur eines neuronalen Netzes ist ein entscheidender Faktor für dessen Leistung und Anpassungsfähigkeit. Jede Schicht des Netzes hat einen speziellen Zweck und kann dazu beitragen, unterschiedliche Aspekte deiner Daten zu modellieren. Hier wirst du die Möglichkeit haben, deine eigene Netzwerkarchitektur Schicht für Schicht aufzubauen.",
            dropoutIntro: "Manchmal wird Dropout als Parameter in anderen Layern verwendet und manchmal als eigenständiger Layer. Der Unterschied liegt hauptsächlich in der Implementierung und Flexibilität. Durch das Hinzufügen eines eigenen Dropout-Layers können Sie den Dropout gezielt auf bestimmte Schichten anwenden und die Architektur des Netzwerks anpassen. Ein Dropout-Parameter innerhalb eines anderen Layers bietet eine kompaktere Möglichkeit, Dropout für diesen speziellen Layer hinzuzufügen.",

            dropout: "Dropout-Rate",

            denseNeurons: "Neuronenzahl",
            denseActivation: "Aktivierungsfunktion",
            denseRegularization: "Regularisierung",

            recNeurons: "Einheiten (Neuronen)",
            recActivation: "Aktivierungsfunktion",
            recRegularization: "Regularisierung",
            recDropout: "Rekurrenter Dropout",
            recSequence: "Rückgabesequenzen",

            dtreeMaxDepth: "Maximale Tiefe",
            dtreeMaxFeatures: "Maximale Merkmale",
            dtreeMinSampleSplit: "Minimale Proben zum Teilen",
            dtreeMinSamplesLeaf: "Minimale Proben pro Blatt",
            dtreeMissingValues: "Behandlung von fehlenden Werten",
            dtreeQuality: "Qualitätsmaß",

            svmC: "C (Regularisierung)",
            svmDegree: "Degree (für Polynomial Kernel)",

            linregRegularization: "Regularisierungstyp",
            logregRegularization: "Regularisierungstyp",
        },
        option: {
            dtreeMean: "Mittelwert",
            dtreeMedian: "Median",
            dtreeDrop: "Verwerfen",
            dtreeGini: "Gini-Unreinheit",
            dtreeEntropy: "Entropie",

            linregRegularizationNone: "Keine Regularisierung",
            linregRegularizationL1: "L1-Regularisierung (Lasso)",
            linregRegularizationL2: "L2-Regularisierung (Ridge)",
        },
        helptext: {
            denseneuron: "Bestimmt die Anzahl der Neuronen in dieser Schicht. Mehr Neuronen erlauben komplexere Modelle, können aber auch zu Overfitting führen.",
            lstmNeuron: "In LSTM-Layern bezieht sich der Begriff 'Einheiten' auf die Anzahl der LSTM-Zellen in der Schicht. Jede Einheit enthält den Mechanismus des LSTM, einschließlich der verschiedenen Gates und des internen Zellzustands. Sie können sich eine Einheit grob als ein 'erweitertes Neuron' vorstellen, das speziell für sequenzielle Daten entwickelt wurde.",
            gruNeuron: "In GRU-Layern bezieht sich der Begriff 'Einheiten' auf die Anzahl der GRU-Zellen in der Schicht. Jede Einheit enthält den Mechanismus des GRU, einschließlich der verschiedenen Gates. Sie können sich eine Einheit grob als ein 'erweitertes Neuron' vorstellen, das speziell für sequenzielle Daten entwickelt wurde.",
            activationFunction: "Definiert, wie die Ausgabe eines Neurons berechnet wird.",
            activationFunctionGru: "Definiert, wie die Ausgabe eines Neurons berechnet wird. Typischerweise wird Tanh für GRU-Aktivierungen verwendet. ReLU kann in manchen Fällen funktionieren, birgt aber das Risiko des Gradienten-Explodierens.",
            activationFunctionLstm: "Definiert, wie die Ausgabe eines Neurons berechnet wird. Typischerweise wird Tanh für LSTM-Aktivierungen verwendet. ReLU kann in manchen Fällen funktionieren, birgt aber das Risiko des Gradienten-Explodierens.",
            activationFunctionRelu: "Beliebt für Zwischenschichten, da es schnelles Training ermöglicht und nicht saturiert. Es setzt alle negativen Werte auf null und behält positive Werte bei. Aber Vorsicht vor \"toten\" Neuronen, die nie aktiviert werden. ",
            activationFunctionSigmoid: "Begrenzt Ausgaben zwischen 0 und 1. Sie eignet sich besonders gut für Ausgabeschichten von binären Klassifikationsproblemen. Wegen ihrer sättigenden Natur und der Gefahr des verschwindenden Gradienten nicht für tiefe Netzwerke empfohlen.",
            activationFunctionTanh: "Wie Sigmoid, aber mit Ausgaben zwischen -1 und 1. Es zentriert die Daten, was oft vorteilhaft ist, aber es kann auch saturieren.",
            regularizationTypeNet: "Reduziert Overfitting durch Bestrafen großer Gewichtswerte.",
            regularizationTypeNetL1: "Bestraft die absolute Größe der Gewichte; kann zu spärlichen Gewichtsmatrizen führen.",
            regularizationTypeNetL2: "Bestraft das Quadrat der Gewichtsgrößen; gebräuchlicher als L1.",
            regularizationTypeNetLambda: "Der Lambda-Wert steuert die Stärke der Regularisierung. Ein höherer Wert führt zu stärkerer Regularisierung, wodurch das Modell vorsichtiger wird und große Gewichtswerte vermeidet. Dies kann Overfitting reduzieren, aber ein zu hoher Wert kann zu Underfitting führen. Beginnen Sie mit einem kleinen Wert (z.B. 0,001) und justieren Sie bei Bedarf.",
            dropout: "Ein Mechanismus gegen Overfitting. Ein bestimmter Prozentsatz der Neuronen wird zufällig in jedem Trainingsschritt \"ausgeschaltet\".",

            lstmSequence: "Aktivieren Sie diese Option, wenn Sie die gesamte Sequenz der Ausgaben anstelle der letzten Ausgabe zurückgeben möchten. Dies ist besonders nützlich, wenn Sie eine zeitliche Abfolge an einen anderen LSTM- oder rekurrenten Layer weitergeben möchten. Wenn Sie beispielsweise ein Modell für die Satzgenerierung oder die Vorhersage von Zeitreihen mit mehreren zukünftigen Punkten erstellen, benötigen Sie die vollständige Sequenz.",
            gruSequence: "Aktivieren Sie diese Option, wenn Sie die gesamte Sequenz der Ausgaben anstelle der letzten Ausgabe zurückgeben möchten. Dies ist besonders nützlich, wenn Sie eine zeitliche Abfolge an einen anderen GRU- oder rekurrenten Layer weitergeben möchten. Wenn Sie beispielsweise ein Modell für die Satzgenerierung oder die Vorhersage von Zeitreihen mit mehreren zukünftigen Punkten erstellen, benötigen Sie die vollständige Sequenz.",

            recDropout: "Während \"Dropout\" die Eingabeverbindungen zu den LSTM-Einheiten beeinflusst, betrifft \"rekurrenter Dropout\" die rekurrenten Verbindungen innerhalb der Einheiten. Dies kann dabei helfen, Overfitting in zeitlichen Sequenzen zu verhindern.",

            dtreeMaxDepth: "Die maximale Tiefe des Baumes. Ein tieferer Baum wird genauer sein, aber das Risiko des Overfittings steigt.",
            dtreeMaxFeatures: "Die maximale Anzahl von Merkmalen, die für das beste Teilen gesucht werden. Ein kleinerer Wert kann den Algorithmus schneller machen.",
            dtreeMinSampleSplit: "Dieser Parameter gibt die minimale Anzahl von Datensätzen an, die benötigt wird, um einen Knoten im Baum zu teilen. Wenn die Anzahl der Datensätze in einem Knoten unter diesem Wert liegt, wird der Knoten nicht weiter geteilt. Dies hilft, kleine und feinverzweigte Bäume zu verhindern, die zu Überanpassung führen können.",
            dtreeMinSamplesLeaf: "Die minimale Anzahl von Datensätzen, die in einem Blattknoten (Endknoten) des Baums enthalten sein müssen. Das Erhöhen dieser Zahl kann das Modell stabiler und weniger anfällig für Rauschen in den Daten machen. Allerdings kann ein zu hoher Wert die Fähigkeit des Modells einschränken, feinere Muster zu erkennen.",
            dtreeMissingValues: "Bestimmt, wie mit fehlenden Werten umgegangen wird. Sie können entweder durch den Mittelwert, den Median ersetzt oder komplett verworfen werden.",
            dtreeQuality: "Das Maß, das verwendet wird, um die Qualität eines Splits zu bewerten. Gini-Unreinheit und Entropie sind die gängigsten Methoden.",
            dtreeGini: "Die Gini-Unreinheit misst, wie oft ein zufällig ausgewähltes Element fälschlicherweise klassifiziert werden würde, wenn es zufällig nach der Verteilung der Labels in einem Knoten klassifiziert wird. Eine Gini-Unreinheit von 0 bedeutet, dass alle Elemente im Knoten zu einer einzelnen Klasse gehören, d.h. der Knoten ist \"rein\". Ein höherer Wert für die Gini-Unreinheit zeigt eine größere Vermischung von Klassen in einem Knoten an. Bei der Entscheidungsbaum-Bildung versucht man, Attribute zu finden, die die Gini-Unreinheit minimieren.",
            dtreeEntropy: "Entropie ist ein Maß für die Unordnung oder den Informationsgehalt. In Bezug auf Entscheidungsbäume misst die Entropie, wie gemischt die Daten in Bezug auf das Label in einem Knoten sind. Ein Knoten mit einer Entropie von 0 ist völlig \"rein\", d.h. alle Datenpunkte in diesem Knoten gehören zu einer Klasse. Ein Knoten mit einer höheren Entropie enthält eine Mischung von Klassen. Bei der Entscheidungsbaum-Bildung wird versucht, Attribute zu finden, die die Entropie minimieren, wodurch der Informationsgewinn maximiert wird.",

            svmKernel: "Der Kernel bestimmt den Typ der Hyperebene, die zur Trennung der Daten verwendet wird. Unterschiedliche Kernel können verschiedene Formen von Trennungen erzeugen.",
            svmKernelLinear: "Dieser Kernel erstellt eine lineare Trennlinie. Er ist ideal für Daten, bei denen die Klassen durch eine gerade Linie oder Ebene getrennt werden können.",
            svmKernelPolynomial: "Dieser Kernel erstellt eine polynomiale Trennlinie, was nützlich ist, wenn die Daten in einer polynomialen Form getrennt werden müssen.",
            svmKernelRBF: "Dies ist ein häufig verwendeter Kernel, der es ermöglicht, eine nichtlineare Trennung in der Form einer Glockenkurve zu erstellen. Er ist besonders nützlich, wenn die Trennung der Daten nicht einfach ist.",
            svmKernelSigmoid: "Dieser Kernel erzeugt eine trennende Hyperplane in der Form einer Sigmoid-Funktion.",
            svmC: "Der Regularisierungsparameter C ist ein Trade-off zwischen der Erzielung eines möglichst großen Abstands zwischen der trennenden Hyperplane und den Datenpunkten und der Minimierung der Klassifikationsfehler. Ein kleiner Wert von C versucht, die Margin zwischen den Datenpunkten und der trennenden Linie so groß wie möglich zu halten, was zu einer glatteren Entscheidungsgrenze führen kann, während es einige Misclassifikationen zulässt. Ein hoher Wert von C legt mehr Wert darauf, alle Datenpunkte korrekt zu klassifizieren, kann jedoch zu Overfitting führen, da die Entscheidungsgrenze zu stark an die Trainingsdaten angepasst wird.",
            svmDegree: "Der Grad des Polynomial-Kernels bestimmt die Art der polynomialen Trennung zwischen den Daten. Ein höherer Grad führt zu komplexeren Trennungen. Es ist wichtig zu beachten, dass ein zu hoher Grad zu Overfitting führen kann, da die Entscheidungsgrenze zu genau an die Trainingsdaten angepasst wird. Der Degree-Parameter wird nur verwendet, wenn der Kernel auf \"Polynomial\" gesetzt ist.",
            svmDegreeHint: "Degree wird nur beim Polynomial-Kernel verwendet.",

            linregRegularization: "Der Regularisierungstyp steuert die Art der Strafe, die auf das Modell angewendet wird, um Overfitting zu vermeiden.",
            linregRegularizationL1: "Fördert das Modell, dünn besetzte Gewichte zu lernen, wodurch irrelevante oder schwach wirkende Features eliminiert werden können.",
            linregRegularizationL2: "Fügt der Fehlerfunktion eine Strafterm hinzu, die proportional zum Quadrat der Summe der Gewichte ist. Dies führt dazu, dass die Gewichte kleiner werden, aber nicht auf Null reduziert werden.",
            linregAlpha: "Der Alpha-Parameter kontrolliert die Stärke der Regularisierung. Ein höherer Alpha-Wert führt zu einer stärkeren Regularisierung. Die optimale Wahl von Alpha ist datenabhängig und sollte durch Cross-Validation bestimmt werden.",

            logregRegularization: "Der Regularizer Typ bestimmt die Art der Regularisierung, die auf die Kostenfunktion angewendet wird.",
            logregRegularizationL1: "L1-Regularisierung fügt einen Strafterm hinzu, der proportional zur absoluten Größe der Gewichte ist.",
            logregRegularizationL2: "L2-Regularisierung fügt einen Strafterm hinzu, der proportional zum Quadrat der Gewichte ist.",
            logregSolver: "Der Solver bestimmt die Optimierungsalgorithmen, die zur Lösung des Optimierungsproblems verwendet werden. Die Auswahl des Solvers kann sich auf die Konvergenzgeschwindigkeit und die Leistung der logistischen Regression auswirken.",
            logregSolverNewton: "Ein numerischer Optimierungsalgorithmus, der auf der Berechnung der exakten Hesse-Matrix basiert. Er ist für kleine Datensätze geeignet, aber möglicherweise nicht für große Datensätze aufgrund der Berechnungskomplexität der Hesse-Matrix.",
            logregSolverL: "Ein quasi-newtonsches Optimierungsverfahren, das auf einer begrenzten Speicherimplementierung des BFGS-Verfahrens basiert. Es ist in der Regel effizienter als der Newton-CG-Solver für große Datensätze.",
            logregSolverLiblinear: "Eine der populärsten Bibliotheken für lineare Modelle, die lineare SVMs und logistische Regression für große Datensätze effizient unterstützt. Es verwendet eine verbesserte Version des Coordinate Descent-Verfahrens.",
            logregSolverSAG: "Stochastic Average Gradient Descent ist eine Variante des Gradientenabstiegs, die für große Datensätze geeignet ist. Sie arbeitet schneller als normale Gradientenabstiegsverfahren, indem sie den Durchschnitt der Gradienten über mehrere zufällige Batches berechnet.",
            logregSolverSAGA: "Eine Verbesserung des SAG-Verfahrens, das eine zusätzliche Korrektur für die Richtung des Gradienten einführt, um die Konvergenzgeschwindigkeit zu erhöhen. Es ist gut für konvexe Probleme und hat eine schnelle Konvergenz.",
            logregLambda: "Lambda ist ein Hyperparameter, der nur für Modelle mit Regularisierung relevant ist. Er kontrolliert die Stärke der Regularisierung. Ein höherer Wert von Lambda kann dazu führen, dass die Koeffizienten näher oder gleich Null werden, was zu einer stärkeren Regularisierung führt.",

            initModelchangeHint: "Der Modelltyp kann nicht nachträglich geändert werden.",
        },
        headline: {
            tasktype: "Aufgabentyp",
            modeltype: "Modelltyp",
            layertype: "Add a layer",
            architecture: "Modellarchitektur",
        },
        card: {
            classificationLabel: "Klassifizierung",
            classificationDescription: "Die Klassifizierung ist ein Überwachtes Lernen, bei dem das Ziel darin besteht, eine Eingabe (wie ein Bild, Text oder ein anderer Datentyp) einer der vordefinierten Kategorien zuzuordnen. Beispielsweise könnte man versuchen, Bilder von Tieren als \"Hund\", \"Katze\" oder \"Vogel\" zu klassifizieren.",
            regressionLabel: "Regressionsanalyse",
            regressionDescription: "Regressionsanalysen versuchen, den Zusammenhang zwischen Eingabevariablen (Merkmale) und einer kontinuierlichen Ausgabevariable (Ziel) zu modellieren. Zum Beispiel könnte man den Preis eines Hauses basierend auf Merkmalen wie Größe, Lage und Alter vorhersagen.",

            dtreeLabel: "Decision Tree",
            dtreeSubLabel: "(Entscheidungsbaum)",
            dtreeDescription: "Entscheidungsbäume teilen Daten rekursiv in Untergruppen, um Entscheidungen auf der Grundlage von Eingabemerkmalen zu treffen.",

            logregLabel: "Logistische Regression",
            logregDescription: "Dieses Modell schätzt die Wahrscheinlichkeit, dass eine gegebene Eingabe zu einer bestimmten Klasse gehört. Es ist besonders nützlich für binäre Klassifizierungsaufgaben, kann aber auch für mehrklassige Aufgaben verwendet werden.",

            svmLabel: "SVM",
            svmSubLabel: "(Support Vector Machines)",
            svmDescription: "Ein Modell, das versucht, eine optimale Trennlinie oder -fläche zwischen Datenklassen zu finden. Es ist besonders nützlich für komplexe Klassifikationsprobleme, kann aber auch für Regression verwendet werden.",

            linregLabel: "Lineare Regression",
            linregDescription: "Dieses Modell versucht, einen linearen Zusammenhang zwischen den Eingabemerkmalen und dem Zielwert zu finden.",

            nnLabel: "Neuronales Netz",
            nnDescription: "Ein komplexes Modell, das aus vielen miteinander verbundenen \"Neuronen\" besteht. Es kann nicht-lineare Zusammenhänge modellieren und ist für eine Vielzahl von Aufgaben geeignet, einschließlich Regressionsproblemen.",

            rnnLabel: "RNN",
            rnnSubLabel: "(Recurrent Neural Network)",
            rnnDescription: "Eine spezielle Art von neuronalem Netzwerk, das für sequentielle Daten konzipiert ist. Es ist besonders nützlich für Zeitreihen- oder Textdaten.",

            denseLabel: "Dense",
            denseSubLabel: "(Vollverknüpft)",
            denseDescription: "Dies sind die Standard-Schichten, die du in den meisten neuronalen Netzen findest. Jedes Neuron in einer Dense-Schicht ist mit jedem Neuron in der vorherigen Schicht verbunden.",
            lstmLabel: "LSTM",
            lstmSubLabel: "(Long Short-Term Memory)",
            lstmDescription: "Spezialisierte rekurrente Schicht, die langfristige Abhängigkeiten in sequentiellen Daten modelliert. Ideal für Zeitreihen und Aufgaben mit zeitlichen Abhängigkeiten durch ihre \"Gates\", die entscheiden, welche Informationen gespeichert oder verworfen werden.",
            gruLabel: "GRU",
            gruSubLabel: "(Gated Recurrent Unit)",
            gruDescription: "Rekurrente Schicht ähnlich wie LSTM, jedoch mit einfacherer Struktur. Kombiniert bestimmte Gates für Effizienz und ist oft schneller im Training. Gut für sequentielle Mustererkennung.",
            dropoutLabel: "Dropout",
            dropoutDescription: "Eine Technik zur Reduzierung von Overfitting, bei der zufällig ausgewählte Neuronen während des Trainingsprozesses \"ausgeschaltet\" werden.",
        },
        navigation: {
            modelselection: "Modellauswahl",
            architecture: "Architektur",
            training: "Training",
        }
    }
}