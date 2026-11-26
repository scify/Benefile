import { Plus } from 'lucide-react'
// Each part from its own file: the kit's index also loads its brand logos,
// which this scene does not use.
import type { Act } from '../kit/act'
import { Button } from '../kit/controls/Button'
import { Field } from '../kit/controls/Field'
import { Collapse } from '../kit/motion/Collapse'
import { Avatar } from '../kit/parts/Avatar'
import { Stage } from '../kit/stage'
import { Text, Window } from '../kit/window'
import './benefile.css'
import mark from './benefile-mark.png'

// BeneFile's landing page hero. One beneficiary's file, the medical folder
// open. The doctor adds a visit: the new row opens at the top of the medical
// history, and the file history beside the window gets the doctor's line,
// above the psychologist's and the lawyer's. One file,
// each profession in its own folder.
//
// Real strings: the beneficiary, the four folder tabs (they are the point),
// the visit form's labels and what is typed, the history rows, and the file
// history. The words are the application's own, in the page's language.

export type Lang = 'en' | 'el'

const WORDS = {
  en: {
    tabs: ['Basic information', 'Medical folder', 'Legal folder', 'Psychosocial folder'],
    tabsShort: ['Basic', 'Medical', 'Legal', 'Psychosocial'],
    menu: ['Beneficiaries', 'Registration', 'Search', 'Reports'],
    fileNo: 'File no. KK1',
    search: 'File number…',
    title: 'Medical history',
    newVisit: 'New visit',
    date: 'Date of examination',
    diagnosis: 'Diagnosis',
    medication: 'Medication',
    save: 'Save medical visit',
    head: ['Date', 'Doctor', 'Place', 'Diagnosis'],
    history: 'File history',
    typedDiagnosis: 'Respiratory infection',
    typedMedicine: 'Paracetamol 500mg',
    doctor: 'Maria Papadopoulou',
    justNow: 'Just now',
    medicalFolder: 'Medical folder',
    places: { athens: 'Athens Polyclinic', day: 'Athens Day Centre', thessaloniki: 'Thessaloniki Polyclinic' },
    visits: [
      ['03-06-2026', 'Nikos Georgiou', 'day', 'Low back pain'],
      ['18-01-2026', 'Maria Papadopoulou', 'athens', 'Asthma flare-up'],
      ['24-11-2025', 'Christina Dimaki', 'athens', 'Ear infection'],
      ['09-09-2025', 'Nikos Georgiou', 'day', 'Sprained ankle'],
      ['12-05-2025', 'Christina Dimaki', 'thessaloniki', 'Skin rash'],
      ['27-02-2025', 'Maria Papadopoulou', 'athens', 'Vaccination'],
      ['14-01-2025', 'Nikos Georgiou', 'day', 'Flu'],
    ],
    people: [
      ['Sofia Michailidou', 'Psychosocial folder', '21 Oct'],
      ['Dimitris Alexiou', 'Legal folder', '2 Oct'],
      ['Katerina Ioannou', 'Basic information', '1 Sep'],
    ],
  },
  el: {
    tabs: ['Βασικά στοιχεία', 'Ιατρικός φάκελος', 'Νομικός φάκελος', 'Ψυχοκοιν. φάκελος'],
    tabsShort: ['Βασικά', 'Ιατρικός', 'Νομικός', 'Ψυχοκοιν.'],
    menu: ['Ωφελούμενοι', 'Εγγραφή', 'Αναζήτηση', 'Αναφορές'],
    fileNo: 'Α/Φ KK1',
    search: 'Αριθμός φακέλου…',
    title: 'Ιατρικό ιστορικό',
    newVisit: 'Νέα επίσκεψη',
    date: 'Ημερ. εξέτασης',
    diagnosis: 'Διάγνωση',
    medication: 'Φαρμακευτική αγωγή',
    save: 'Αποθήκευση ιατρικής επίσκεψης',
    head: ['Ημερ.', 'Γιατρός', 'Τοποθεσία', 'Διάγνωση'],
    history: 'Ιστορικό φακέλου',
    typedDiagnosis: 'Λοίμωξη αναπνευστικού',
    typedMedicine: 'Paracetamol 500mg',
    doctor: 'Μαρία Παπαδοπούλου',
    justNow: 'Τώρα',
    medicalFolder: 'Ιατρικός φάκελος',
    places: { athens: 'Πολυιατρείο Αθήνας', day: 'Κέντρο Ημέρας Αθήνας', thessaloniki: 'Πολυιατρείο Θεσσαλονίκης' },
    visits: [
      ['03-06-2026', 'Νίκος Γεωργίου', 'day', 'Οσφυαλγία'],
      ['18-01-2026', 'Μαρία Παπαδοπούλου', 'athens', 'Παρόξυνση άσθματος'],
      ['24-11-2025', 'Χριστίνα Δημάκη', 'athens', 'Ωτίτιδα'],
      ['09-09-2025', 'Νίκος Γεωργίου', 'day', 'Διάστρεμμα αστραγάλου'],
      ['12-05-2025', 'Χριστίνα Δημάκη', 'thessaloniki', 'Δερματικό εξάνθημα'],
      ['27-02-2025', 'Μαρία Παπαδοπούλου', 'athens', 'Εμβολιασμός'],
      ['14-01-2025', 'Νίκος Γεωργίου', 'day', 'Γρίπη'],
    ],
    people: [
      ['Σοφία Μιχαηλίδου', 'Ψυχοκοινωνικός φάκελος', '21 Οκτ'],
      ['Δημήτρης Αλεξίου', 'Νομικός φάκελος', '2 Οκτ'],
      ['Κατερίνα Ιωάννου', 'Βασικά στοιχεία', '1 Σεπ'],
    ],
  },
} as const

type Words = (typeof WORDS)[Lang]

type Focus = 'diagnosis' | 'medicine' | null

type S = {
  form: boolean
  focus: Focus
  diagnosis: string
  medicine: string
  saved: boolean
  logged: boolean
}

const initial: S = { form: false, focus: null, diagnosis: '', medicine: '', saved: false, logged: false }
const POSTER: S = { ...initial, saved: true, logged: true }

const TODAY = '12-11-2026'
const REST = { wide: { x: 760, y: 236 }, compact: { x: 300, y: 196 } }

function scriptFor(w: Words) {
  return async function script(a: Act<S>) {
    await a.wait(1000)

    // 1. The doctor opens the form of a new visit
    a.mark('open')
    await a.move({ at: 'new-visit', x: 0.8, y: 0.6 })
    await a.wait(200)
    await a.click()
    await a.set({ form: true })
    await a.wait(700)

    // 2. Diagnosis and medicine; the date is today's already
    a.mark('type')
    await a.move({ at: 'diagnosis', x: 0.88, y: 0.55 })
    await a.click()
    await a.set({ focus: 'diagnosis' })
    await a.type('diagnosis', w.typedDiagnosis, { interval: 42 })
    await a.wait(300)
    await a.move({ at: 'medicine', x: 0.88, y: 0.55 })
    await a.click()
    await a.set({ focus: 'medicine' })
    await a.type('medicine', w.typedMedicine, { interval: 42 })
    await a.wait(400)

    // 3. Save: the new row opens as the form closes, so the rows below make
    // one move; the file history takes the doctor's line a beat later
    a.mark('save')
    await a.move({ at: 'save', x: 0.75, y: 0.6 })
    await a.wait(150)
    await a.click()
    await a.set({ focus: null, form: false })
    await a.wait(120)
    await a.set({ saved: true })
    await a.wait(640)
    await a.set({ logged: true })
    a.mark('payoff')
    await a.move('rest')
    await a.wait(2600)

    // What the story added leaves together, the last change first. The typed
    // words are cleared while the form is closed.
    a.mark('reset')
    await a.set({ logged: false })
    await a.wait(70)
    await a.set({ saved: false })
    await a.wait(700)
    await a.snap({ diagnosis: '', medicine: '' })
    await a.wait(300)
  }
}

const SCRIPTS = { en: scriptFor(WORDS.en), el: scriptFor(WORDS.el) }

function Tabs({ w, compact }: { w: Words; compact: boolean }) {
  // The phone design names the folders in one word, so all four stay whole
  const tabs = compact ? w.tabsShort : w.tabs
  return (
    <div className="bf-tabs">
      {tabs.map((t, i) => (
        <span key={t} className="bf-tab" data-on={i === 1 || undefined}>
          {t}
        </span>
      ))}
    </div>
  )
}

function SideMenu({ w }: { w: Words }) {
  return (
    <div className="bf-side">
      <img className="bf-side-mark" src={mark} alt="" width={44} height={44} />
      <div className="bf-side-search">
        <Text className="bf-side-placeholder">{w.search}</Text>
      </div>
      <div className="bf-side-item" data-on>
        <Text>{w.fileNo}</Text>
      </div>
      {w.menu.map((item) => (
        <div key={item} className="bf-side-item">
          <Text>{item}</Text>
        </div>
      ))}
    </div>
  )
}

function VisitRow({ cells, fresh }: { cells: readonly string[]; fresh?: boolean }) {
  const [date, doctor, place, diagnosis] = cells
  return (
    <div className="bf-row" data-fresh={fresh || undefined}>
      <Text className="bf-cell bf-cell--date">{date}</Text>
      <Text className="bf-cell">{doctor}</Text>
      <Text className="bf-cell bf-cell--faint">{place}</Text>
      <Text className="bf-cell">{diagnosis}</Text>
    </div>
  )
}

function Folder({ s, w, compact }: { s: S; w: Words; compact: boolean }) {
  return (
    <div className="bf-card">
      <div className="bf-card-head">
        <div className="bf-card-title">{w.title}</div>
        <Button data-target="new-visit" icon={<Plus size={14} strokeWidth={2.4} />}>
          {w.newVisit}
        </Button>
      </div>

      <Collapse open={s.form} reveal="drop">
        <div className="bf-form">
          {/* Filled in already; the phone design leaves it out so Save stays in frame */}
          {!compact && <Field className="bf-field" label={w.date} value={TODAY} />}
          <Field
            className="bf-field"
            label={compact ? undefined : w.diagnosis}
            placeholder={compact ? w.diagnosis : undefined}
            value={s.diagnosis}
            focused={s.focus === 'diagnosis'}
            box={{ 'data-target': 'diagnosis', 'data-cursor': 'ibeam' }}
          />
          <Field
            className="bf-field"
            label={compact ? undefined : w.medication}
            placeholder={compact ? w.medication : undefined}
            value={s.medicine}
            focused={s.focus === 'medicine'}
            box={{ 'data-target': 'medicine', 'data-cursor': 'ibeam' }}
          />
          <div className="bf-form-actions">
            <Button variant="primary" data-target="save">
              {w.save}
            </Button>
          </div>
        </div>
      </Collapse>

      <div className="bf-table" data-crop>
        <div className="bf-row bf-row--head">
          {w.head.map((h) => (
            <span key={h} className="bf-cell">
              {h}
            </span>
          ))}
        </div>
        <Collapse open={s.saved}>
          <VisitRow cells={[TODAY, w.doctor, w.places.athens, w.typedDiagnosis]} fresh />
        </Collapse>
        {w.visits.map(([date, doctor, place, diagnosis]) => (
          <VisitRow key={date} cells={[date, doctor, w.places[place], diagnosis]} />
        ))}
      </div>
    </div>
  )
}

function FileHistory({ s, w }: { s: S; w: Words }) {
  return (
    <div className="bf-history">
      <div className="bf-history-title">
        <span>{w.history}</span>
      </div>
      <Collapse open={s.logged}>
        <HistoryRow name={w.doctor} folder={w.medicalFolder} when={w.justNow} fresh />
      </Collapse>
      {/* The card shows the latest work and keeps its size: the oldest line
          leaves as the new one arrives, so its edges never move */}
      {w.people.map(([name, folder, when], i) =>
        i === w.people.length - 1 ? (
          <Collapse key={name} open={!s.logged}>
            <HistoryRow name={name} folder={folder} when={when} />
          </Collapse>
        ) : (
          <HistoryRow key={name} name={name} folder={folder} when={when} />
        ),
      )}
    </div>
  )
}

function HistoryRow({ name, folder, when, fresh }: { name: string; folder: string; when: string; fresh?: boolean }) {
  return (
    <div className="bf-history-row" data-fresh={fresh || undefined}>
      <Avatar name={name} size={28} tone={fresh ? 'accent' : 'person'} />
      <div className="bf-history-text">
        <Text className="bf-history-name">{name}</Text>
        <Text className="bf-history-folder">{folder}</Text>
      </div>
      <Text className="bf-history-when">{when}</Text>
    </div>
  )
}

export function BenefileHero({ lang = 'en' }: { lang?: Lang }) {
  const w = WORDS[lang]
  return (
    <Stage
      width={1120}
      height={520}
      compact={{ below: 860, width: 420, height: 400 }}
      accent="#00a59a"
      fidelity="realistic"
      initial={initial}
      poster={POSTER}
      script={SCRIPTS[lang]}
      cursor={{ at: REST }}
      className="bf"
    >
      {(s, _rect, layout) => {
        const compact = layout === 'compact'
        return (
          <>
            <Window url="benefile.example.org" urlShape="pill" className="bf-window">
              <div className="bf-app">
                <div className="bf-top">
                  <span className="bf-top-title">Karim Nasser / KK1</span>
                  {!compact && <span className="bf-top-user">{w.doctor}</span>}
                </div>
                <div className="bf-body">
                  {!compact && <SideMenu w={w} />}
                  <div className="bf-main">
                    <Tabs w={w} compact={compact} />
                    <div className="bf-page">
                      <Folder s={s} w={w} compact={compact} />
                    </div>
                  </div>
                </div>
              </div>
            </Window>
            {!compact && <FileHistory s={s} w={w} />}
            <span data-target="rest" style={{ position: 'absolute', left: REST[layout].x, top: REST[layout].y }} />
          </>
        )
      }}
    </Stage>
  )
}
